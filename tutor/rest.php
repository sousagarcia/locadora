<?php
// initialization script
require_once 'init.php';

class AdiantiRestServer
{
    public static function run($request)
    {
        $class   = isset($request['class']) ? $request['class']   : '';
        $method  = isset($request['method']) ? $request['method'] : '';
        $response = NULL;
        
        // aqui implementar mecanismo de controle !!
        if (!in_array($class, array('ProductService')))
        {
            return json_encode( array('status' => 'error',
                                      'data'   => _t('Permission denied')));
        }
        
        try
        {
            if (class_exists($class))
            {
                if (method_exists($class, $method))
                {
                    $rf = new ReflectionMethod($class, $method);
                    if ($rf->isStatic())
                    {
                        $response = call_user_func(array($class, $method), $request);
                    }
                    else
                    {
                        $response = call_user_func(array(new $class($request), $method), $request);
                    }
                    return json_encode( array('status' => 'success', 'data' => $response));
                }
                else
                {
                    $error_message = TAdiantiCoreTranslator::translate('Method ^1 not found', "$class::$method");
                    return json_encode( array('status' => 'error', 'data' => $error_message));
                }
            }
            else
            {
                $error_message = TAdiantiCoreTranslator::translate('Class ^1 not found', $class);
                return json_encode( array('status' => 'error', 'data' => $error_message));
            }
        }
        catch (Exception $e)
        {
            return json_encode( array('status' => 'error', 'data' => $e->getMessage()));
        }
    }
}

print AdiantiRestServer::run($_REQUEST);
