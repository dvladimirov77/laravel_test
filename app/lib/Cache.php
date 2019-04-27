<?php

namespace lib;

use Memcached;

/**
 *    Обёртка для Memcache
 *    имеется небольшая засада - при "старте сайта" обёртка накидает кучу локов...
 *    в случае если на false кеша драйвер бд не приучен создавать кеш - будут проблемы :(
 */
class Cache
{
    private $expire;
    private $mmc;
    public $touch = false; //обновление тайминга
    public $lock_pause = 10; //пауза для лока
    public $key_sep = "-_-"; //разделитель ключей в тегах

    /**
     * конструктор класса, поднимает соединение
     *86400
     * @param int $exp
     */
    public function __construct($exp = 86400)
    {
		
		
//~ MEMCACHE_NAME_SPACE=sotland
//~ MEMCACHE_HOST_NAME=127.0.0.1
//~ MEMCACHE_PORT=11211
		
		
		$this->name_space = Config::read('memcache.name_space');
		$this->host_name = Config::read('memcache.host_name');
		$this->port = Config::read('memcache.port');
		
        $this->expire = $exp;

		$mc = new Memcached();
		if ($mc->addServer($this->host_name, $this->port)) $this->mmc = $mc;
		
		$this->mmc->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
    }

    /**
     * метод получает данные из кеша
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
		$key = str_replace(" ", "_", $key);
		
        $result = $this->mmc->get($this->name_space . $key);
        if ($result)
        {
            if ($this->touch) $this->mmc->touch($this->name_space . $key, $this->expire);
            return $result;
        }
        elseif (!$this->mmc->get($this->name_space . $key . "_lock"))
        {
            $this->mmc->add($this->name_space . $key . "_lock", time());
        }
        else return $this->check_lock($this->name_space . $key);/* лок установлен, ждём снятия */

        return false;
    }

    /**
     * метод сохраняет запись
     *
     * @param string $key
     * @param mixed $value
     * @param array $tags
     */
    public function set($key, $value, $tags = false)
    {
		$key = str_replace(" ", "_", $key);
		
        $this->mmc->set($this->name_space . $key, $value, $this->expire);
        
        if ($this->mmc->get($this->name_space . $key . "_lock"))
            $this->mmc->delete($this->name_space . $key . "_lock");/* убираем лок */
        if (is_array($tags) and count($tags))
            $this->set_tags($key, $tags);
    }

    /**
     * метод удаляет запись
     *
     * @param string $key
     */
    public function delete($key = false)
    {
		$key = str_replace(" ", "_", $key);
		
		$this->mmc->delete($this->name_space . $key);
    }

    /**
     * метод удаляет записи по ключам в теге
     *
     * @param string $tag
     */
    public function del_tag($tag)
    {
        $str = $this->mmc->get($this->name_space . "tag_" . $tag);
        if ($str)
        {
            $keys = explode($this->key_sep, $str);
            foreach ($keys as $key) $this->mmc->delete($this->name_space . str_replace(" ", "_", $key));
            $this->mmc->delete("tag_" . $tag);
        }
    }

    /**
     * метод создаёт записи о ключах для тегов
     *
     * @param string $key
     * @param array $tags
     */
    private function set_tags($key, $tags)
    {
		$key = str_replace(" ", "_", $key);
		
        foreach ($tags as $tag)
        {
			$old = $this->mmc->get($this->name_space . "tag_" . $tag);
			if ($old) $this->mmc->set($this->name_space . "tag_" . $tag, $old . $this->key_sep . $key, $this->expire);
			else $this->mmc->set($this->name_space . "tag_" . $tag, $key, $this->expire);
		}
    }

    /**
     * метод ждёт снятия лока и возвращает данные для исключения dog-pile effect
     *
     * @param string $key
     * @return mixed
     */
    private function check_lock($key)
    {
		$key = str_replace(" ", "_", $key);
		
        while ($this->mmc->get($this->name_space . $key . "_lock"))
        {
            if ($this->mmc->get($this->name_space . $key . "_lock") > (time() + $this->lock_pause + 1))
                return false; //аварийный выход
        }
        return $this->mmc->get($this->name_space . $key);
    }
}