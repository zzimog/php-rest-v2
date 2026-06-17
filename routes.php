<?php

use Core\Router;
use Core\Endpoint;
use Http\JsonResponse;

try {
    new Router("/api/v2")

        /** Simple test route */
        ->route("/foo", "GET", function () {
            $resp = new JsonResponse();
            $resp->setValue("message", "This is Foo.");
            $resp->send();
        })

        /** Mount subroute */
        ->mount("/hello")

        /** With parameter */
        ->route("/:par", "GET", function (string $par) {
            $resp = new JsonResponse();
            $resp->setValue("message", "Hello to: {$par}");
            $resp->send();
        })

        /** Root */
        ->route("/", "GET", function () {
            $resp = new JsonResponse();
            $resp->setValue("message", "Hello to everyone");
            $resp->send();
        })

        /** Unmount sub-route */
        ->unmount()

        /** Test route with Endpoint class */
        ->route("/bar", "GET", new Endpoint("This is Bar."))

        /** Run the router */
        ->run();
} catch (\Error $e) {
    new JsonResponse(404)
        ->setValue("timestamp", date("c"))
        ->setValue("status", $e->getCode())
        ->setValue("error", $e->getMessage())
        ->setValue("message", "Path requested not found.")
        ->send();
}
