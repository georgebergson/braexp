<?php

class ConsumoPnce
{
    private $opt;
    private $env;

    /**
     *
     */
    public function __construct($opt = array())
    {        
        $this->env = $this->getEnvData();
        $this->setAllOpt($opt);
    }

    /**
     *
     */
    public function setAllOpt($opt)
    {
        foreach (CURLOPT as $key => $value) {
            $this->opt[$key] = isset($opt[$key]) ? $opt[$key] : $value;
        }

        if (empty($this->opt['url'])) {
            $this->opt['url'] = ($this->env)['url'];
        }

        $nm = $this->env['name'];
        $this->opt['sslcert'] = ROOT.'/config/'.$nm.'_'.$this->opt['sslcert'];
        $this->opt['sslkey'] = ROOT.'/config/'.$nm.'_'.$this->opt['sslkey'];
        $this->opt['cainfo'] = ROOT.'/config/'.$nm.'_'.$this->opt['cainfo'];
    }

    /**
     *
     */
    public function setOpt($key, $value)
    {
        return $this->opt[$key] = $value;
    }

    /**
     *
     */
    public function getAllOpt()
    {
        return $this->opt;
    }

    /**
     *
     */
    public function getOpt($key)
    {
        return $this->opt[$key];
    }

    /**
     *
     */
    public function getEnvData()
    {
        foreach (ENV as $key => $value) {
            if (strpos($_SERVER['HTTP_HOST'], $key) !== false) {
                return $value;
            }
        }

        return ENV['default'];
    }

    /**
     *
     */
    public function criarCertificados($filename, $password)
    {
        $filename = ROOT.'/config/'.$filename;

        if (!$pkcs12 = file_get_contents($filename)) {
            echo "Error: Unable to read the cert file\n";
            exit;
        }

        if (!openssl_pkcs12_read($pkcs12, $result, $password)) {
            echo "Error: Unable to read the cert store.\n";
            exit;
        }

        file_put_contents($this->opt['sslcert'], $result['cert']);
        file_put_contents($this->opt['sslkey'], $result['pkey']);
        file_put_contents($this->opt['cainfo'], $result['extracerts']);
    }

    /**
     *
     */
    public function curl_init($uri = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->opt['url'] . $uri);
        curl_setopt($ch, CURLOPT_SSLVERSION, $this->opt['sslversion']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->opt['returntransfer']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->opt['ssl_verifypeer']);
        curl_setopt($ch, CURLOPT_SSLCERT, $this->opt['sslcert']);
        curl_setopt($ch, CURLOPT_SSLKEY, $this->opt['sslkey']);
        curl_setopt($ch, CURLOPT_CAINFO, $this->opt['cainfo']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->opt['timeout']);
        curl_setopt($ch, CURLOPT_HEADER, $this->opt['header']);
        return $ch;
    }

    /**
     *
     */
    public function getAvaliacaoMaturidade($cpf, $exec = 1)
    {
        $ch = $this->curl_init('api/AvaliacaoMaturidade/' . $cpf);
        return $exec ? curl_exec($ch) : $ch;
    }

    /**
     *
     */
    public function getListaAcoes($cpf, $exec = 1)
    {
        $ch = $this->curl_init('api/ListaAcoes/' . $cpf);
        return $exec ? curl_exec($ch) : $ch;
    }

    /**
     *
     */
    public function getFormEntryMeta($cpf, $exec = 1)
    {
        $ch = $this->curl_init('api/FormEntryMeta/' . $cpf);
        return $exec ? curl_exec($ch) : $ch;
    }

    /**
     *
     */
    public function postCriarUsuarios($postdata, $exec = 1)
    {
        $ch = $this->curl_init('api/CriarUsuarios');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        return $exec ? curl_exec($ch) : $ch;
    }

    /**
     *
     */
    public function postSalvarQuestionarios($postdata, $exec = 1)
    {
        $ch = $this->curl_init('api/SalvarQuestionarios');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        return $exec ? curl_exec($ch) : $ch;
    }
}