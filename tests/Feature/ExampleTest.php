<?php

test('the home page redirects to the help queue', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('tickets.index'));
});
