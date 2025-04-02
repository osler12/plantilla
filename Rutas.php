<?php

class Rutas
{
    private $_controlador;
    private $_metodo;
    private $_argumentos;

    public function __construct()
    {
        if (isset($_GET['url'])) {
            $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL); // ELIMINA CARACTERES ESPECIALES
            $url = explode('/', $url);  // ARREGLO A PARTIR DE /

            $this->_controlador = array_shift($url);  // QUITA UN ELEMENTO DE ARREGLO
            $this->_metodo = array_shift($url);   // QUITA UN ELEMENTO DE ARREGLO
            $this->_argumentos = $url;  // EL RESTO SON ARGUMENTOS
        }

        // Si no se proporciona un controlador, usa el valor por defecto
        if (!$this->_controlador) {
            $this->_controlador = DEFAULT_CONTROLLER;
        }

        // Si no se proporciona un método, usa 'index' por defecto
        if (!$this->_metodo) {
            $this->_metodo = 'index';
        }

        // Asegúrate de que _argumentos esté inicializado como un arreglo vacío si es null
        if (!isset($this->_argumentos)) {
            $this->_argumentos = array();
        }

        // Usar strtolower solo si las variables no son null
        $this->_controlador = strtolower($this->_controlador ?? '');  // Si _controlador es null, convierte a cadena vacía
        $this->_metodo = strtolower($this->_metodo ?? '');  // Si _metodo es null, convierte a cadena vacía
    }

    public function run()
    {
        $rutaControlador = ROOT . 'Controllers' . DS . $this->_controlador . 'Controller.php';
        $controller = $this->_controlador . 'Controller';
        $metodo = $this->_metodo;
        $args = $this->_argumentos;

        if (is_readable($rutaControlador)) {
            require_once $rutaControlador;
            $controller = new $controller;

            // Verifica si el método es accesible y llamable
            if (is_callable(array($controller, $metodo))) {
                $metodo = $this->_metodo;
            } else {
                $metodo = 'index';
            }

            // Llama al método del controlador con o sin argumentos
            if (isset($args)) {
                call_user_func_array(array($controller, $metodo), $args);
            } else {
                call_user_func_array(array($controller, $metodo));
            }
        } else {
            throw new Exception("Controlador no encontrado: " . $rutaControlador);
        }
    }

    public function getControlador()
    {
        return $this->_controlador;
    }

    public function getMetodo()
    {
        return $this->_metodo;
    }

    public function getArgs()
    {
        return $this->_argumentos;
    }
}

