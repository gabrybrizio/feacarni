<?php

$settings = array (
    'strict' => false,
    'debug' => false,
    
    'baseurl' => 'https://localhost:8000/',

    'security' => array (
        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,

    ),

    'sp' => array (
        'entityId' => 'https://localhost:8000/',
        'assertionConsumerService' => array (
            'url' => 'https://localhost:8000/pages/sso.php?login-response',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ),
        'singleLogoutService' => array (
            'url' => 'https://localhost:8000/pages/sso.php?logout-response',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
        'x509cert' => 'MIIC8DCCAdigAwIBAgIQSAI9PikKfYpE/1pryhT+bTANBgkqhkiG9w0BAQsFADA0 MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZp Y2F0ZTAeFw0yMDEwMjExNjUxNTFaFw0yMzEwMjExNjUxNTJaMDQxMjAwBgNVBAMT KU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjAN BgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArA7zJwTEvecNnpEMCFeV5oDIgKX1 A0V7C/wBxghJNVEwmhP9LdS816btFO/QzyOCVA/dhr1RJFbmBYLwveMlpzdENeRt lM09pdKXw/BBJKKgGrfKfXUxccouiCOnOSsMVJ7XCw7O5m7r3aUJWvwz0AlixLb4 xIr5J93NVw/k8kh/x2vW5AXi/FM4EeXQmxSraoxOgk9IsIzM5F8HpN9qRSY2ZjLE AzFvfIZ6ay/FPFG3wlhP0hk4k59z0T4Sxt9fb3o+HajHifYVWjNWsNeM2Pcg8nfm fSsciFJRv4cTpCtMa1KeiZy9poY/d6bhqP/WAJrIv2vovs4bH/Jm+7mU1QIDAQAB MA0GCSqGSIb3DQEBCwUAA4IBAQA7NiC41uBwFAcJIZRKPj9iZYIrQP4Hxk03lZ6r suQDDm5CuVwatucpCfTxTB5TlRNyFhIJyQ1QHZ4HeWvi8sfv6c8crffPxG0WnCtf EktANo7FigotEVP3yk7wTsfmwQN8lLecM+XUFOMGudiv7wutmCbRvtMnp6LcuHK+ 2egZj+u1oL+h3eSNj1TgWxnJZgSWMI2qM4GVAme1E20T2PXyLx0PDygGm6e8K7c0 V2evsj/OQDG1DKJDNlktvvUW4Zd5GRuAUVjIrXCJKDl5M995Loq1aMG39usJWXac PMptVhkEbDxiSYkJnmWm6dsN3+mUs7cbTgaQtfEPOEu45o89',
        'privateKey' => '',
    ),

    'idp' => array (
        'entityId' => 'https://sts.windows.net/1b3669b1-64c0-4588-b3a3-409437f391e4/',
        'singleSignOnService' => array (
            'url' => 'https://login.microsoftonline.com/1b3669b1-64c0-4588-b3a3-409437f391e4/saml2',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        'singleLogoutService' => array (
            //'url' => 'https://login.microsoftonline.com/common/wsfederation?wa=wsignout1.0',
            'url' => 'https://login.microsoftonline.com/1b3669b1-64c0-4588-b3a3-409437f391e4/saml2',
            'responseUrl' => 'https://localhost:8000/pages/sso.php?logout-response',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        'x509cert' => 'MIIC8DCCAdigAwIBAgIQSAI9PikKfYpE/1pryhT+bTANBgkqhkiG9w0BAQsFADA0 MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZp Y2F0ZTAeFw0yMDEwMjExNjUxNTFaFw0yMzEwMjExNjUxNTJaMDQxMjAwBgNVBAMT KU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjAN BgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArA7zJwTEvecNnpEMCFeV5oDIgKX1 A0V7C/wBxghJNVEwmhP9LdS816btFO/QzyOCVA/dhr1RJFbmBYLwveMlpzdENeRt lM09pdKXw/BBJKKgGrfKfXUxccouiCOnOSsMVJ7XCw7O5m7r3aUJWvwz0AlixLb4 xIr5J93NVw/k8kh/x2vW5AXi/FM4EeXQmxSraoxOgk9IsIzM5F8HpN9qRSY2ZjLE AzFvfIZ6ay/FPFG3wlhP0hk4k59z0T4Sxt9fb3o+HajHifYVWjNWsNeM2Pcg8nfm fSsciFJRv4cTpCtMa1KeiZy9poY/d6bhqP/WAJrIv2vovs4bH/Jm+7mU1QIDAQAB MA0GCSqGSIb3DQEBCwUAA4IBAQA7NiC41uBwFAcJIZRKPj9iZYIrQP4Hxk03lZ6r suQDDm5CuVwatucpCfTxTB5TlRNyFhIJyQ1QHZ4HeWvi8sfv6c8crffPxG0WnCtf EktANo7FigotEVP3yk7wTsfmwQN8lLecM+XUFOMGudiv7wutmCbRvtMnp6LcuHK+ 2egZj+u1oL+h3eSNj1TgWxnJZgSWMI2qM4GVAme1E20T2PXyLx0PDygGm6e8K7c0 V2evsj/OQDG1DKJDNlktvvUW4Zd5GRuAUVjIrXCJKDl5M995Loq1aMG39usJWXac PMptVhkEbDxiSYkJnmWm6dsN3+mUs7cbTgaQtfEPOEu45o89',
    ),
);
