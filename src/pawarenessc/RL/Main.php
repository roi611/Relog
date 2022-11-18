<?php

namespace pawarenessc\RL;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use pocketmine\event\Listener;
use pocketmine\player\Player;

use pocketmine\scheduler\Task;

use pocketmine\plugin\PluginBase;

use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\TransferPacket;

class Main extends PluginBase implements Listener
{
		
		
		public function onEnable():void
    	{
    		$this->getLogger()->info("§l§b=========================");
 			$this->getLogger()->info("§l§6Relog§fを読み込みました");
 			$this->getLogger()->info("§l制作者: §ePawarenessC");
 			$this->getLogger()->info("§lライセンス: §aNYSL Version 0.9982");
 			$this->getLogger()->info("§lhttp://www.kmonos.net/nysl/");
 			$this->getLogger()->info("§lバージョン:{$this->getDescription()->getVersion()}");
 			$this->getLogger()->info("§l§b=========================");
 			$this->getServer()->getPluginManager()->registerEvents($this,$this);
 			$this->con = new Config($this->getDataFolder()."ip.yml", Config::YAML,
			[
				"ip" => $this->getServer()->getIp(),
				"port" => $this->getServer()->getPort(),
			]);
  		}
  		
  	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
  	{
        
        if(!$sender instanceof Player){
        	$sender->sendMessage('ゲーム内で実行してください');
            return true;
        }
        
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
			
			$sender->getNetworkSession()->sendDataPacket($pk);
			break;
		
			case "setip":
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
		if($pk instanceof ModalFormResponsePacket) {
			$player = $event->getOrigin()->getPlayer();
			$id = $pk->formId;
			$data = $pk->formData;
			$result = json_decode($data);
			if($data === "null\n") {
			}else{
				if ($id === 73612) {
					$port = intval($result[2]); //string型で返されるからint型に変換する
					$this->con->set("ip",$result[1]);
					$this->con->set("port",$port);
					$this->con->save();
					
					$player->sendMessage("§lIP:§6{$result[1]}");
					$player->sendMessage("§lPORT:§6{$port}");
					$player->sendMessage("設定が完了しました。");
				}
			}
		}
	}
	
	public function createWindow(Player $player, $data, int $id){
		$pk = new ModalFormRequestPacket();
		$pk->formId = $id;
		$pk->formData = json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_UNESCAPED_UNICODE);
		$player->getNetworkSession()->sendDataPacket($pk);
	}
}
