<?php
    require __DIR__ . '/../../app/init.php';

    $orderId = r::get('order-id');
    $articleId = r::get('article-id');
    $variantId = r::get('variant-id');

    if (!util::isGUID($orderId) || !util::isGUID($articleId) || !util::isGUID($variantId)) {
        redirect::to('/');
        die();
    }

    $article = db::table('articoli')->fetch('Article')->where('GUID', '=', $articleId)->first();

    if (!$article) {
        redirect::to('/');
        die();
    }

    $article->selectVariant($variantId);

    if (order::orderContains($orderId, $articleId, $variantId)) {
        $file = $article->digitalDownloadFile();
        f::download($file);
    } else {
        redirect::to('/');
        die();
    }
?>

