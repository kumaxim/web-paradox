<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontpageController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function whois(Request $request)
    {
        $domain = $request->input('domain');

        if (is_string($domain) && strlen($domain)) {
            if (preg_match('/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/', $domain)) {
                $parser = new \Novutec\WhoisParser\Parser();
                $lookup = $parser->lookup($domain);
                $whois = $lookup->toArray();

                if (array_key_exists('expires', $whois) && is_string($whois['expires']) && strlen($whois['expires'])) {
                    return json_encode(
                        ['domain' => $domain, 'valid' => true, 'registered' => true, 'expires' => $whois['expires']]
                    );
                }

                return json_encode(['domain' => $domain, 'valid' => true, 'registered' => false]);
            }
        }

        return json_encode(['domain' => $domain,'valid' => false]);
    }
}
