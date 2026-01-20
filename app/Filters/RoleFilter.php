<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role = $session->get('role');

        if (!$role) {
            return redirect()->to('/login');
        }

        if ($arguments && !in_array($role, $arguments, true)) {
            return service('response')
                ->setStatusCode(403)
                ->setBody('Forbidden: insufficient role');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
