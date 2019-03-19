<?php

/**
 * Container holding values which can be resolved upon reading and optionally stored and shared
 * across reads.
 *
 * <code>
 * $c = new RokUpdater_Container();
 *
 * $c->setFactory('foo', 'Foo_factory'); // $c will be passed to Foo_factory()
 * $c->foo; // new Foo instance
 * $c->foo; // same instance
 *
 * $c->setFactory('bar', 'Bar_factory', false); // non-shared
 * $c->bar; // new Bar instance
 * $c->bar; // different Bar instance
 *
 * $c->setValue('a_string', 'foo_factory'); // don't call this
 * $c->a_string; // 'foo_factory'
 * </code>
 *
 */
class RokUpdater_Container implements ArrayAccess
{

	/**
	 * @var array each element is an array: ['callable' => mixed $factory, 'shared' => bool $isShared]
	 */
	protected $factories = array();

	/**
	 * @var array
	 */
	protected $cache = array();

	const CLASS_NAME_PATTERN_52 = '/^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$/i';
	const CLASS_NAME_PATTERN_53 = '/^(\\\\?[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*)+$/i';

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 * </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 *       The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->cache);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 * </p>
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset)
	{
		return $this->$offset;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 * </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 * </p>
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->$offset = $value;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 * </p>
	 *
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	/**
	 * Fetch a value.
	 *
	 * @param string $name The name of the value to fetch
	 *
	 * @return mixed
	 * @throws RokUpdater_Exception
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->cache)) {
			return $this->cache[$name];
		}
		if (!isset($this->factories[$name])) {
			throw new RokUpdater_Exception("Value or factory was not set for: $name");
		}
		$value = $this->build($this->factories[$name]['callable'], $name);
		if ($this->factories[$name]['shared']) {
			$this->cache[$name] = $value;
		}
		return $value;
	}

	/**
	 * Build a value
	 *
	 * @param mixed  $factory The factory for the value
	 * @param string $name    The name of the value
	 *
	 * @return mixed
	 * @throws RokUpdater_Exception
	 */
	protected function build($factory, $name)
	{
		if (is_callable($factory)) {
			return call_user_func($factory, $this);
		}
		$msg = "Factory for '$name' was uncallable";
		if (is_string($factory)) {
			$msg .= ": '$factory'";
		} elseif (is_array($factory)) {
			if (is_string($factory[0])) {
				$msg .= ": '{$factory[0]}::{$factory[1]}'";
			} else {
				$msg .= ": " . get_class($factory[0]) . "->{$factory[1]}";
			}
		}
		throw new RokUpdater_Exception($msg);
	}

	/**
	 * Set a value to be returned without modification
	 *
	 * @param string $name  The name of the value
	 * @param mixed  $value The value
	 *
	 * @return RokUpdater_Container
	 * @throws InvalidArgumentException
	 */
	public function setValue($name, $value)
	{
		$this->remove($name);
		$this->cache[$name] = $value;
		return $this;
	}

	/**
	 * Set a factory to generate a value when the container is read.
	 *
	 * @param string   $name     The name of the value
	 * @param callable $callable Factory for the value
	 * @param bool     $shared   Whether the same value should be returned for every request
	 *
	 * @return RokUpdater_Container
	 * @throws InvalidArgumentException
	 */
	public function setFactory($name, $callable, $shared = true)
	{
		if (!is_callable($callable, true)) {
			throw new InvalidArgumentException('$factory must appear callable');
		}
		$this->remove($name);
		$this->factories[$name] = array(
			'callable' => $callable,
			'shared'   => $shared
		);
		return $this;
	}

	/**
	 * Set a factory based on instantiating a class with no arguments.
	 *
	 * @param string $name       Name of the value
	 * @param string $class_name Class name to be instantiated
	 * @param bool   $shared     Whether the same value should be returned for every request
	 *
	 * @return RokUpdater_Container
	 * @throws InvalidArgumentException
	 */
	public function setClassName($name, $class_name, $shared = true)
	{
		$classname_pattern = version_compare(PHP_VERSION, '5.3', '<') ? self::CLASS_NAME_PATTERN_52 : self::CLASS_NAME_PATTERN_53;
		if (!is_string($class_name) || !preg_match($classname_pattern, $class_name)) {
			throw new InvalidArgumentException('Class names must be valid PHP class names');
		}
		$func = create_function('', "return new $class_name();");
		return $this->setFactory($name, $func, $shared);
	}

	/**
	 * Remove a value from the container
	 *
	 * @param string $name The name of the value
	 *
	 * @return RokUpdater_Container
	 */
	public function remove($name)
	{
		unset($this->cache[$name]);
		unset($this->factories[$name]);
		return $this;
	}

	/**
	 * Does the container have this value
	 *
	 * @param string $name The name of the value
	 *
	 * @return bool
	 */
	public function has($name)
	{
		return isset($this->factories[$name]) || array_key_exists($name, $this->cache);
	}
}