<?php

/* @var $app Slim\App */
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get("/user/{id}", function(Request $req, Response $res) {
    $route = $req->getAttribute('route');
    $user_id = $route->getArgument('id');

    $user = User::getByUuid($user_id);

    return $res->withJson($user);
});

$app->post("/user/add", function(Request $req, Response $res) {
    $user = $req->getParsedBody();
    $user_info = [];

    foreach ($user as $k => $v) {
        if (trim(explode("_", $k)[0]) !== "user") {
            $user_info[$k] = $v;
            unset($user[$k]);
        }
    }

    $type = trim(collectionGetValue($user, "user_type"));

    if (empty($type) || !in_array($type, User::getEnums("user_type"))) {
        $response = $res->withJson([
            "user_type" => "Tipo non riconosciuto!"
        ]);
        return $response->withStatus(400);
    }

    $errors = [];

    switch ($type) {
        case User::TYPE_BUSINESS:
            $company_name = collectionGetValue($user, "user_company_name");
            $company_pec = collectionGetValue($user, "user_company_pec");
            $company_sdi_code = collectionGetValue($user, "user_company_sdi_code");
            $company_vat_number = strtoupper(collectionGetValue($user, "user_company_vat_number"));

            switch (true) {
                case empty($company_name):
                    $errors["user_company_name"] = "Il nome dell'attività è obbligatorio!";
                case empty($company_pec):
                    $errors["user_company_pec"] = "L'indirizzo PEC è obbligatorio!";
                case strpos($company_pec, "@") === false:
                    $errors["user_company_pec"] = "L'indirizzo PEC non è corretto!";
                case empty($company_sdi_code):
                    $errors["user_company_sdi_code"] = "Il codice SDI è obbligatorio!";
                case empty($company_vat_number) || !in_array(strlen($company_vat_number), [16, 18]):
                    $errors["user_company_vat_number"] = "La partita IVA è obbligatoria!";
            }

            $firstname = null;
            $lastname = null;
            $birthdate = null;
            $qualification_id = null;
            break;
        case User::TYPE_PRIVATE:
            $firstname = collectionGetValue($user, "user_firstname");
            $lastname = collectionGetValue($user, "user_lastname");
            $birthdate = collectionGetValue($user, "user_birthdate");
            $qualification_id = collectionGetValue($user, "user_qualification_id");

            switch (true) {
                case empty($firstname):
                    $errors["user_firstname"] = "Il nome è obbligatorio!";
                case empty($lastname):
                    $errors["user_lastname"] = "Il cognome è obbligatorio!";
            }

            if (!empty($birthdate)) {
                try {
                    $birthdate = new DateTime($birthdate);
                } catch (Exception $ex) {
                    $errors["user_birthdate"] = "La data di nascita non è corretta!";
                }
            }

            if (!empty($qualification_id)) {
                //TBI: Verificare l'esistenza della qualification
                $qualification = Qualification::resolve($qualification_id);
                    
                if (is_null($qualification)) {
                    $errors["user_qualification_id"] = "Il titolo di studio non è stato riconosciuto!";
                }
            }

            $company_name = null;
            $company_sdi_code = null;
            $company_pec = null;
            $company_vat_number = null;
            break;
    }

    $fiscal_code = strtoupper(collectionGetValue($user, "user_fiscal_code"));
    $email = collectionGetValue($user, "user_email");
    $password = collectionGetValue($user, "user_password");
    $confirm_password = collectionGetValue($user, "user_confirm_password");
    $privacy_policy = !!collectionGetValue($user, "user_privacy_policy", false);
    $promo = !!collectionGetValue($user, "user_promo", false);
    $newsletter = !!collectionGetValue($user, "user_newsletter", false);

    if (empty($fiscal_code)) {
        $errors[] = "user_fiscal_code";//Mandatory in business, private non ce l'ha proprio
    } elseif (strlen($fiscal_code) == 16) {
        $pattern = "/[A-Z]{6}[0-9]{2}[A-Z]{1}[0-9]{2}[A-Z]{1}[0-9]{3}[A-Z]{1}/";

        if (!preg_match($pattern, $fiscal_code)) {
            if ($type == "business") {
                $pi = strlen($company_vat_number) == 18 ? substr($company_vat_number, 2) : $company_vat_number;

                if ($fiscal_code !== $pi) {
                    $errors[] = "user_fiscal_code";
                }
            } else {
                $errors[] = "user_fiscal_code";
            }
        }
    } elseif (strlen($fiscal_code) == 18) {
        if ($type != "business") {
            $errors[] = "user_fiscal_code";
        } elseif ($fiscal_code !== $company_vat_number) {
            $errors[] = "user_fiscal_code";
        }
    } else {
        $errors[] = "user_fiscal_code";
    }

    $pattern = "/\w{1,}@\w{1,}\.\w{1,}/"; //Verify only the basics because a non existant address is not better than a malformed one!

    if (empty($email)) {
        $errors[] = "user_email";
    } elseif (!preg_match($pattern, $email)) {
        $errors[] = "user_email";
    }

    if (empty($password)) {
        $errors[] = "user_password";
    } else {
        if (strlen($password) < 8) {
            $errors[] = "user_password";
        } else {
            $number_pattern = "/.*?\d.*?/";
            $lower_pattern = "/.*?[a-z].*?/";
            $upper_pattern = "/.*?[A-Z].*?/";

            if (!preg_match($number_pattern, $password) || !preg_match($lower_pattern, $password) || !preg_match($upper_pattern, $password)) {
                $errors[] = "user_password";
            }

            if ($confirm_password !== $password) {
                $errors[] = "user_confirm_password";
            }
        }
    }

    if (!$privacy_policy) {
        $errors[] = "user_privacy_policy";
    }

    if (!empty($errors)) {
        $errors = array_unique($errors);
        $response = $res->withJson($errors);
        return $response->withStatus(400, $text);
    }
    
    $salt = generateCode();
    $activation_code = strtoupperr(generateCode(6));
    
    $user = [
        "user_type" => $type,
        "user_firstname" => $firstname,
        "user_lastname" => $lastname,
        "user_company_name" => $company_name,
        "user_company_sdi_code" => $company_sdi_code,
        "user_company_pec" => $company_pec,
        "user_company_vat_number" => $company_vat_number,
        "user_fiscal_code" => $fiscal_code,
        "user_birthdate" => $birthdate,
        "user_qualification_id" => $qualification_id,
        "user_email" => $email,
        "user_salt" => $salt,
        "user_password" => md5($salt . md5($password) . $salt),
        "user_deleted" => 0,
        "user_privacy_policy" => $privacy_policy ? 1 : 0,
        "user_promo" => $promo ? 1 : 0,
        "user_newsletter" => $newsletter ? 1 : 0,
        "user_activation_code" => $activation_code
    ];
    
    $pdo->beginTransaction();
    
    try {
        $user_id = $pdo->insert("user", $user);
        $pdo->commit();
        
        if ($user_info) {
            //TBI: Inserimento degli indirizzi
        }
    } catch (Exception $ex) {
        $pdo->rollback();
        error_log(json_encode($ex));
        return $res->withStatus(500);
    }

    return $res->withJson(["user_id" => $user_id]);
});

$app->delete("/products/{id}", function(Request $req, Response $res) use ($pdo) {
    $route = $req->getAttribute('route');
    $product_id = $route->getArgument('id');

    $deleted_rows = $pdo->delete("product", ["product_id" => $product_id]);

    return $res->withJson($deleted_rows);
});

$app->post("/products/{id}/setFeatured", function(Request $req, Response $res) use ($pdo) {

    $route = $req->getAttribute('route');
    $product_id = $route->getArgument('id');
    $is_featured = $req->getQueryParam('is_featured');

    if ($is_featured === true) {
        $result = $pdo->insert("featured_product", ["featured_product_product_id" => $product_id]);
    } else {
        $result = $pdo->delete("featured_product", ["featured_product_product_id" => $product_id]);
    }
});
