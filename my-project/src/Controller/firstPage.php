<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 08.06.2018
 * Time: 11:40
 */

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;


class firstPage
{

    public function date()
    {
        $today=date('d-m-Y');

        return new Response('<html><body> <h1>Witaj na stronie</h1><p>Dziś jest: '.$today.'</p> <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum interdum imperdiet diam, a fermentum diam consectetur at. Fusce quis mauris eu ex fringilla fringilla. Sed nec sapien et nibh vehicula placerat. Fusce condimentum porta dictum. Sed ac est cursus, elementum velit ac, tempor lectus. Curabitur lobortis est et nulla elementum, vitae congue lectus condimentum. Vivamus feugiat, velit id tristique semper, urna diam lacinia sapien, et molestie eros turpis quis risus. Phasellus gravida ante odio, at ullamcorper nunc sodales at. Vivamus venenatis varius erat ac hendrerit. Suspendisse leo purus, rutrum sed nulla id, varius cursus orci.</p></body></html>'
        );
    }

}