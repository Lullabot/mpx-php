<?php

namespace Mpx;

interface ClientInterface {

    public function request($method = 'GET', $url = NULL, UserInterface $user = NULL, array $options = []);

}
