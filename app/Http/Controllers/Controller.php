<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Gardashlar Admin API",
 *     description="Bu sənədləşmə Gardashlar admin panel API-ləri üçündür.",
 *     @OA\Contact(
 *         email="info@gardashlar.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=https://admin.gardaslar.coder.az,
 *     description="Əsas server"
 * )
 */
abstract class Controller
{
    //
}
