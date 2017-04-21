
This version supports >= PHP 5.5.9. If you have PHP 5.6 or greater we suggest you to use https://github.com/idandtrust/goodid-php-sdk instead.

# GoodID SDK for PHP 5.5

This repository contains the open source PHP SDK that allows you to validate the signature of the received JWS from your PHP app.


## Installation

The GoodID PHP SDK can be installed with [Composer](https://getcomposer.org/). Add the GoodID PHP SDK package to your `composer.json` file.

```json
{
    "require": {
        "goodid/goodid-php55-sdk": "~1.0"
    }
}
```

## Usage

> **Note:** This version of the GoodID SDK for PHP requires PHP 5.5.9 or greater.

1. Put the following code into the page where you display the "Sign in with GoodID" button

    ```php
    $goodId = new GoodID\Authentication\ImplicitAuthentication($clientId);

    $nonce = $goodId->generateNonce();
    $state = $goodId->generateState();

    // Take the `$nonce` and `$state` into the frontend
    // by e.g. adding those to your template
    // to be able to easily reach them with javascript
    ```

2. When you receive the End User's data from GoodID into your javascript, 
pass the result of `GoodID.getData()`into your backend 
by an AJAX request to be able to do the following:

    Get all the claims combined from the idToken and userInfo:

    ```php
    $goodId = new GoodID\Authentication\ImplicitAuthentication($clientId);

    try {
        $allClaims = $goodId->getClaims($jwsIdToken, $jwsUserInfo, $receivedState);

        // If you want to accept only verified e-mail addresses
        // (so you have requested it with email_verified:{essential:true})
        // you have to check that the received e-mail is indeed verified:
        $userData = $allClaims->get('claims');
        if (!$userData['email_verified']) {
            throw new \Exception('The email is not verified, the user can not be logged in!');
        }
    } catch(GoodID\Exception\GoodIDException $e) {
        echo 'GoodID SDK returned an error: ' . $e->getMessage();
        exit;
    }

    echo "The identity of the user is: " . $allClaims->get('sub') . "\n";
    echo "All the requested claims about the user: \n";
    print_r($allClaims->get('claims'));
    ```

    Or you can get the idToken and userInfo data separately, but in this case please make sure the `sub` is the same in `idtoken` and `userinfo`.

    ```php
    $goodId = new GoodID\Authentication\ImplicitAuthentication($clientId);

    try {
        $idTokenClaims = $goodId->getIdTokenClaims($jwsIdToken, $receivedState);
        $userInfoClaims = $goodId->getUserInfoClaims($jwsUserInfo);

        if ($idTokenClaims->get('sub') !== $userInfoClaims->get('sub')) {
            throw new GoodID\Exception\GoodIDException('The idToken and userinfo data belong to different users.');
        }

        // If you want to accept only verified e-mail addresses
        // (so you have requested it with email_verified:{essential:true})
        // you have to check that the received e-mail is indeed verified:
        $userData = $userInfoClaims->get('claims');
        if (!$userData['email_verified']) {
            throw new \Exception('The email is not verified, the user can not be logged in!');
        }
    } catch(GoodID\Exception\GoodIDException $e) {
        echo 'GoodID SDK returned an error: ' . $e->getMessage();
        exit;
    }

    echo "The identity of the user is: " . $idTokenClaims->get('sub') . "\n";
    echo "The idToken content of the user: \n";
    print_r($idTokenClaims->toArray());
    echo "The userInfo claims about the user: \n";
    print_r($userInfoClaims->get('claims'));
    ```

3. Now you can be sure the data that you received from GoodID was securely sent by the End User and you are able to do the followings:
    1. Do your custom validation on `$allClaims` (or `$userInfoClaims`) if you wish
    2. Log in the user
    3. Go back to your frontend with a response for the AJAX request to redirect the user into your Welcome page (or to display the error if there was any.).
