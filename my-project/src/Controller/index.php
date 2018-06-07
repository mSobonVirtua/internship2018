<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 07.06.2018
 * Time: 10:55
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class index
{
    public function data()
    {
        $data=date("d-m-Y");

        return new Response(
            '<html><body><h1>Witaj na stronie</h1> <p>Aktualna data: '.$data.' </p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sit amet iaculis eros. Etiam venenatis consequat eros. Aliquam ut suscipit metus. Duis sit amet nulla dui. Donec pellentesque velit in lacus sodales, eget finibus felis sollicitudin. Vestibulum sapien risus, blandit ac sodales eget, elementum ut odio. Fusce egestas pulvinar arcu.</p></body></html>');
    }

}