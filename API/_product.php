<?php
    use Psr\Http\Message\RequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    require_once "./utilities/utilities.products.php";
    require_once "./utilities/utilities.categories.php";

    $app->get("/products", function(Request $req, Response $res) use ($pdo) {

        $product_category = $req->getQueryParam('product_category');
        $concise = $req->getQueryParam('concise');

        $products = [];
        $sorting = ["product_id" => "DESC", "product_category" => "ASC"];
        if($product_category) {
            $categories = (!is_numeric($product_category)) ? $pdo->select("category", ["category_code*" => $product_category]) : [["category_id" => $product_category]];
            foreach($categories as $category) {
               $products = array_merge($products, $pdo->select("product", ["product_category" => $category["category_id"]], $sorting));
            }
        } else {
            $products = $pdo->select("product", [], $sorting);
        }
        foreach ($products as $k => &$product) {

            if(!$concise) {
                $category_info = CategoryUtils::getCategoryInfo($pdo, $product["product_category"]);
                $product_info  = ProductUtils::getProductInfo($pdo, $product["product_id"]);
                $product = array_merge($product, $product_info, $category_info);
            }

            if($req->getQueryParam('featured') && !ProductUtils::getFeaturedProducts($product["product_id"])) {
                unset($products[$k]);
            }
        }

        return $res->withJson($products);
    });

    $app->get("/products/{id}", function(Request $req, Response $res) use ($pdo) {
        $route        = $req->getAttribute('route');
        $product_id   = $route->getArgument('id');
        
        $info = ProductUtils::getProductInfo($pdo, $product_id);
        $product = $pdo->select("product", ["product_id" => $product_id])[0];
        $product = array_merge($product, $info);

        return $res->withJson($product);
    });

    $app->post("/products/add", function(Request $req, Response $res) use ($pdo) {
        $product = $req->getParsedBody();
        $product_info = [];

        foreach($product as $k => $v) {
            if(trim(explode("_", $k)[0]) !== "product") {
                $product_info[$k] = $v;
                unset($product[$k]);
            }
        }

        $product_id = $pdo->insert("product", $product);

        if($product_info) {
            $pdo->beginTransaction();
            try {
                ProductUtils::setProductInfo($pdo, $product_id, $product_info);
                $pdo->commit();
            } catch (Excpetion $e) {
                $pdo->rollback();
            }

        }

        return $res->withJson(["product_id" => $product_id]);
    });

    $app->delete("/products/{id}", function(Request $req, Response $res) use ($pdo) {
        $route        = $req->getAttribute('route');
        $product_id   = $route->getArgument('id');
        
        $deleted_rows = $pdo->delete("product", ["product_id" => $product_id]);

        return $res->withJson($deleted_rows);
    });

    $app->post("/products/{id}/setFeatured", function(Request $req, Response $res) use ($pdo) {
        
        $route        = $req->getAttribute('route');
        $product_id   = $route->getArgument('id');
        $is_featured  = $req->getQueryParam('is_featured');

        if($is_featured === true) {
            $result = $pdo -> insert("featured_product", ["featured_product_product_id" => $product_id]);
        } else {
            $result = $pdo -> delete("featured_product", ["featured_product_product_id" => $product_id]);
        }
    });