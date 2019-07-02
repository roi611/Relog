<?php

namespace pawarenessc\RL;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use pocketmine\event\Listener;
use pocketmine\Player;

use pocketmine\scheduler\Task;

use pocketmine\plugin\PluginBase;

use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\TransferPacket;

class Main extends pluginBase implements Listener
{
		
		
		public function onEnable()
    	{
    		$this->getLogger()->info("§l§b=========================");
 			$this->getLogger()->info("§l§6Relog§fを読み込みました");
 			$this->getLogger()->info("§l制作者: §ePawarenessC");
 			$this->getLogger()->info("§lライセンス: §aNYSL Version 0.9982");
 			$this->getLogger()->info("§lhttp://www.kmonos.net/nysl/");
 			$this->getLogger()->info("§lバージョン:{$this->getDescription()->getVersion()}");
 			$this->getLogger()->info("§l§b=========================");
 			$this->getServer()->getPluginManager()->registerEvents($this,$this);
 			$this->con = new Config($this->getDataFolder()."Message.yml", Config::YAML,
			[
				"ip" => "xxxx.jp",
				"port" => 19132,
			]);
  		}
  		
  	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
  	{
		$name = $sender->getName();
		
		$config = $this->con;
		$ip = $config->get("ip");
		$port = $config->get("port");
		
		switch($label)
		{
			case "relog":
			$pk =  new TransferPacket();
			$pk->address = $ip;
			$pk->port = $port;
			
			$sender->dataPacket($pk);
			break;
			
			
			case "arelog":
			$pk =  new TransferPacket();
			$pk->address = $ip;
			$pk->port = $port;
			
			Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
			break;
			
			case "setupip":
			$data = [
				"type" => "custom_form",
				"title" => "SETUP",
				"content" => [
					[
						"type" => "label",
						"text" => "このipとportを設定してください"
					],
					[
						"type" => "input",
						"text" => "§lIP",
						"placeholder" => "",
						"default" => "{$ip}",
					],
					[
						"type" => "input",
						"text" => "§lPORT",
						"placeholder" => "",
						"default" => "{$port}",
					]
				]
			];
			$this->createWindow($sender, $data, 73612);
			break;
		}
		return true;
	}
	
	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) {
		$pk = $event->getPacket();
		$player = $event->getPlayer();
		if($pk instanceof ModalFormResponsePacket) {
			$id = $pk->formId;
			$data = $pk->formData;
			$result = json_decode($data);
			if($data === "null\n") {
			}else{
				if ($id === 73612) {
					$this->config->set("ip",$result[1]);
					$this->config->set("port",$result[2]);
					$this->config->save();
				}
			}
		}
	}
}
