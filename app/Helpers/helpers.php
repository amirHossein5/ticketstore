<?php

function short_code(): string
{
    if (app()->has('short_code')) {
        return app('short_code');
    }

    app()->bind('short_code', function () {
        $prefix = now()->format('y').now()->month.now()->day;

        return $prefix.'_'.substr(base64_encode(random_bytes(5)), 0, 7);
    });

    return app('short_code');
}
