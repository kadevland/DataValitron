<?php

namespace kadevland\Valitron;

use Valitron\Validator;



class DataValitron
{
    /**
     * 
     * @var array $data
     */
    protected $data = null;


    /**
     * @var	 array	$fields
     */
    protected $fields = null;

    /**
     * 
     * @var  Valitron\Validator
     */
    protected $validator = null;

    public function __construct($data = array())
    {
        $this->data = $data;
        $this->fields = array_keys($this->rules());
        $this->validator = new Validator($data, $this->fields);
        $this->extendRules($this->validator);
        $this->applyRules();
        $this->applyLabels();
    }


    protected function applyRules()
    {

        $rules = $this->rules();

        foreach ($rules as $field => $fieldRules) {

            $fieldRules = is_array($fieldRules) ? $fieldRules : array($fieldRules);


            foreach ($fieldRules as $fieldRule) {

                $fieldRule = is_array($fieldRule) ? $fieldRule : array($fieldRule);
                $rule = isset($fieldRule[0]) ? $fieldRule[0] : null;
                $params = isset($fieldRule[1]) ? $fieldRule[1] : null;
                $flag = (isset($fieldRule[2]) && is_bool($fieldRule[2])) ? $fieldRule[2] : null;
                $message = (isset($fieldRule[2]) && !is_bool($fieldRule[2])) ? $fieldRule[2] : null;

                if (count($fieldRule) == 4) {
                    $message = (isset($fieldRule[3]) && is_string($fieldRule[3])) ? $fieldRule[3] : null;
                }

                if ($rule) {

                    $setting = array($rule, $field);
                    if (!is_null($params)) {
                        $setting[] = $params;
                    }
                    if (!is_null($flag)) {
                        $setting[] = $flag;
                    }
                    $v =  call_user_func_array(array($this->validator, 'rule'), $setting);

                    if (!is_null($message)) {
                        $v->message($message);
                    }
                }
            }
        }
    }

    protected function applyLabels()
    {

        if (count($this->labels())) {
            $this->validator->labels($this->labels());
        }
    }

    /**
     * Run validations and return boolean result
     * @param mixed|null $data 
     * @return mixed 
     */
    public function validate($data = null)
    {
        if (!is_null($data)) {
            $this->data = $data;
            $this->validator = $this->validator->withData($data, $this->fields);
        }

        return $this->validator->validate();
    }

    /**
     *  Check has error
     * @return bool 
     */
    public function hasErrors()
    {
        return count($this->validator->errors()) != 0;
    }

    /**
     *  Check has error for field
     * @param string $field 
     * @return bool 
     */
    public function hasError($field)
    {
        return $this->validator->errors($field) != false;
    }

    /**
     * Get array of error messages
     *
     * @param  null|string $field
     * @return array|bool
     */
    public function errors($field = null)
    {
        return $this->validator->errors($field);
    }

    /**
     * Should the validation stop a rule is failed
     * @param bool $stop
     */
    public function stopOnFirstFail($stop = true)
    {
        $this->validator->stopOnFirstFail($stop);
        return $this;
    }

    /**
     * rawData 
     * @return array 
     */
    public function rawData()
    {
        return $this->data;
    }

    /**
     * 
     * @return array 
     */
    public function data()
    {

        return $this->validator->data();
    }

    public function fields()
    {

        return $this->fields;
    }

    /**
     * Register new validation rule callback
     *
     * @param string $name
     * @param callable $callback
     * @param string $message
     * @throws \InvalidArgumentException
     */
    public function addRule($name, $callback, $message = null)
    {
        $this->validator->addInstanceRule($name, $callback, $message);
        return $this;
    }

    /**
     * 
     * Define additional rules
     * @return void 
     */
    protected function extendRules()
    {
    }
    /**
     * Define rules
     * @return array 
     */
    protected function rules()
    {
        return array();
    }

    /**
     * Define labels
     * @return array 
     */
    protected function labels()
    {
        return array();
    }
}
