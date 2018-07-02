<?php
/**
 * V12A - stworzenie pierwszej strony
 *
 * @category   Virtua
 * @package    Virtua_Module
 * @copyright  Copyright (c) Virtua
 * @author     dkruczek
 */


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;


class firstPage
{

    public function date()
    {
        $today=date('d-m-Y');

        return new Response('<html><body> <img src="kubek.jpg"><h1>Witaj na stronie</h1><p>Dziś jest: '.$today.'</p> <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum interdum imperdiet diam, a fermentum diam consectetur at. Fusce quis mauris eu ex fringilla fringilla. Sed nec sapien et nibh vehicula placerat. Fusce condimentum porta dictum. Sed ac est cursus, elementum velit ac, tempor lectus. Curabitur lobortis est et nulla elementum, vitae congue lectus condimentum. Vivamus feugiat, velit id tristique semper, urna diam lacinia sapien, et molestie eros turpis quis risus. Phasellus gravida ante odio, at ullamcorper nunc sodales at. Vivamus venenatis varius erat ac hendrerit. Suspendisse leo purus, rutrum sed nulla id, varius cursus orci.</p></body></html>'
        );
    }

}
