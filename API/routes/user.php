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
            $fiscal_code = strtoupper(collectionGetValue($user, "user_fiscal_code"));

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
                case empty($fiscal_code):
                    $errors["user_fiscal_code"] = "Il codice fiscale è obbligatorio!";
                    
                    if (strlen($fiscal_code) == 16) {
                        $pattern = "/[A-Z]{6}[0-9]{2}[A-Z]{1}[0-9]{2}[A-Z]{1}[0-9]{3}[A-Z]{1}/";

                        if (!preg_match($pattern, $fiscal_code)) {
                            $pi = strlen($company_vat_number) == 18 ? substr($company_vat_number, 2) : $company_vat_number;

                            if ($fiscal_code !== $pi) {
                                $errors["user_fiscal_code"] = "Il codice fiscale non è corretto!";
                            }
                        }
                    } elseif (strlen($fiscal_code) == 18 && $fiscal_code !== $company_vat_number) {
                        $errors["user_fiscal_code"] = "Il codice fiscale non è corretto!";
                    } else {
                        $errors["user_fiscal_code"] = "Il codice fiscale non è corretto!";
                    }
            }
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
            } else {
                $birthdate = null;
            }

            if (!empty($qualification_id)) {
                $qualification = Qualification::resolve($qualification_id);
                    
                if (is_null($qualification)) {
                    $errors["user_qualification_id"] = "Il titolo di studio non è stato riconosciuto!";
                }
            } else {
                $qualification_id = null;
            }
            break;
    }
    
    $email = collectionGetValue($user, "user_email");
    $password = collectionGetValue($user, "user_password");
    $confirm_password = collectionGetValue($user, "user_confirm_password");
    $privacy_policy = !!collectionGetValue($user, "user_privacy_policy", false);
    $promo = !!collectionGetValue($user, "user_promo", false);
    $newsletter = !!collectionGetValue($user, "user_newsletter", false);

    $pattern = "/\w{1,}@\w{1,}\.\w{1,}/"; //Verify only the basics because a non existant address is not better than a malformed one!

    if (empty($email)) {
        $errors["user_email"] = "L'email è obbligatoria e sarà utilizzata per il login!";
    } elseif (!preg_match($pattern, $email)) {
        $errors["user_email"] = "L'email inserita non è valida!";
    }

    if (empty($password)) {
        $errors["user_password"] = "La password è obbligatoria!";
    } else {
        if (strlen($password) < 8) {
            $errors["user_password"] = "La password deve essere almeno di 8 caratteri!";
        } else {
            $number_pattern = "/.*?\d.*?/";
            $lower_pattern = "/.*?[a-z].*?/";
            $upper_pattern = "/.*?[A-Z].*?/";

            if (!preg_match($number_pattern, $password) || !preg_match($lower_pattern, $password) || !preg_match($upper_pattern, $password)) {
                $errors["user_password"] = "La password deve obbligatoriamente contenere una lettera maiuscola, una lettera minuscola e una cifra!";
            }

            if ($confirm_password !== $password) {
                $errors["user_confirm_password"] = "La password inserite non coincidono!";
            }
        }
    }

    if (!$privacy_policy) {
        $errors["user_privacy_policy"] = "È necassario aver letto ed accettare la nostra privacy policy!";
    }

    if (!empty($errors)) {
        $response = $res->withJson($errors);
        return $response->withStatus(400);
    }
    
    $db = DB::getInstance();
    $db->startTransaction();
    
    try {
        switch ($type) {
            case User::TYPE_BUSINESS:
                $user_id = User::createBusiness($company_name, $company_sdi_code, $company_pec, $company_vat_number, $fiscal_code, $email, $privacy_policy, $promo, $newsletter);
                break;
            case User::TYPE_PRIVATE:
                $user_id = User::createPrivate($firstname, $lastname, $birthdate, $qualification_id, $email, $privacy_policy, $promo, $newsletter);
                break;
        }
        
        $user = User::resolve($user_id);
        $user->setPassword($password);
        
        if ($user_info) {
            //TBI: Inserimento degli indirizzi
        }
        // TODO: Invio della mail per conferma indirizzo
        $db->commit();
    } catch (Exception $ex) {
        $db->rollback();
        error_log(json_encode($ex));
        return $res->withStatus(500);
    }
    
    return $res->withJson(["user_id" => $user_id]);
});

$app->put("/user/{id}/delete", function(Request $req, Response $res) {
    // IMPORTANTE: VERIFICARE CHE L'UTENTE CHE ESEGUE L'OPERAZIONE SIA ABILITATO A FARLA
    $route = $req->getAttribute('route');
    $user_id = $route->getArgument('id');

    $user = User::getByUuid($user_id);
    
    if (empty($user)) {
        return $res->withStatus(404);
    }
    
    $user->setDeleted();

    return $res->withStatus(204);
});