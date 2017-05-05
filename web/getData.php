<?php 

require('../vendor/autoload.php');
require_once('db.php');

Class dataRetriever
{	
	const HEROKU = 'postgres://jejyhalaallzkh:9504ad344df592eb4f19703507bf906b9edc5560c92a2eb8cf66eadf6a5769ec@ec2-23-23-212-121.compute-1.amazonaws.com:5432/df8d6h3up8bnq3';
	const LOCAL = 'postgres://postgres:bismillah@localhost:5432/hmlite';

	public $app;

	/**
	* Constructor initialize the db connector
	*
	* @param string $url 
	*/
	public function __construct($url = dataRetriever::HEROKU){
		$this->app = connect_db($url);
	}
	/**API ANDROID**/
	/**
	* Get character status [Sementara karakter cuma 1]
	* 
	*/
	public function getStatus($player_id){
		if($player_id == null){
			$player_id = 1;
		}
		$query = "SELECT 
					character_name, character_stamina, inventory_id, character_money, character_max_stamina, character_level, character_exp, character_next_exp, gift_box_id, character_id, character_stamina_recover, character_firebase_id
				FROM 
					public.hm_maincharacter
				WHERE
					character_firebase_id = '{$player_id}' ;";	
		$st = $this->app['pdo']->prepare($query);
		$st->execute();
		$data = array();

		if($row = $st->fetch(PDO::FETCH_ASSOC)){
			$data['character_id'] = $row['character_id'];
            $data['character_firebase_id'] = $row['character_firebase_id'];
			$data['character_name'] = $row['character_name'];
			$data['character_stamina'] = $row['character_stamina'];
			$data['character_max_stamina'] = $row['character_max_stamina'];
			$data['inventory_id'] = $row['inventory_id'];
			$data['character_money'] = $row['character_money'];
			$data['character_level'] = $row['character_level' ];
			$data['character_exp'] = $row['character_exp'];
			$data['character_next_exp'] = $row['character_next_exp'];
			$data['gift_box_id'] = $row['gift_box_id'];
			if($row['character_stamina_recover'] == null){
				$data['character_stamina_recover'] = "";
			}else{
				$data['character_stamina_recover'] = $row['character_stamina_recover'];
			}
		}

		return $data;
	}

	/**
	* Get all id of item from Gacha 
	*
	*/
	public function getAllItemID(){
		$st = $this->app['pdo']->prepare('SELECT item_id from hm_item');
		$st->execute();

		$data = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			array_push($data, $row['item_id']);
		}

		return $data;
	}

	/**
	* Get all id of animal creature for Gacha
	*
	*/
	public function getAllCreatureID(){
		$st = $this->app['pdo']->prepare('SELECT creature_id FROM hm_creature WHERE creature_type = true');
		$st->execute();

		$data = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			array_push($data, $row['creature_id']);
		}

		return $data;
	}


	public function setGachaPrize($item_id, $inventory_id){
		try{
			if($this->addItemtoInventory($item_id, $inventory_id)){
				$query = "SELECT item_name FROM hm_item WHERE item_id = ?";
				$st = $this->app['pdo']->prepare($query);
				$st->bindParam(1, $item_id, PDO::PARAM_INT);
				$st->execute();
				$data = array();
				if($row = $st->fetch(PDO::FETCH_ASSOC)){
					$data['item_name'] = $row['item_name'];
				}
				return $data;
			}else{
				return ("Error");
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public function addItemtoInventory($item_id, $inventory_id)
	{	
		try{
			$query = "SELECT total_item FROM hm_itemtoinventory WHERE item_id = {$item_id} AND inventory_id = {$inventory_id}";
			$st = $this->app['pdo']->prepare($query);
			if($st->execute()){
				if($row = $st->fetch(PDO::FETCH_ASSOC)){
					$total_item = $row['total_item'];
				}
			}
			$total_item++;
			$query1 = 	"UPDATE
							hm_itemtoinventory 
						SET  
							total_item = {$total_item}
						WHERE
							item_id = {$item_id} AND inventory_id = {$inventory_id}";
			$st = $this->app['pdo']->prepare($query1);
			if($st->execute()){
				return true;
			}
		}catch(Exception $ex){
			throw $ex;
		}
	}

	public function getAllPlantInfo(){
		$st = $this->app['pdo']->prepare('SELECT 
												plant_id, plant_phase, creature_id, creature_name, plot_id
											FROM 
												(public.hm_plant 
											INNER JOIN
											    hm_creature 
											ON hm_plant.plant_creature_id = hm_creature.creature_id) INNER JOIN hm_plot ON plant_id = plot_plant_id
											');
		$st->execute();

		$data = array();
		$i = 0;
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			$data[$i]['plant_id'] = $row['plant_id'];
			$data[$i]['plant_phase'] = $row['plant_phase'];
			$data[$i]['plot_id'] = $row['plot_id'];
			$data[$i]['creature_name'] = $row['creature_name'];
			$data[$i]['creature_id'] = $row['creature_id'];
			$i++;
		}
		return $data;
	}


	public function getPlantInfoByID($id){
		$query = 'SELECT 
					plant_birth_time, plant_id, plant_phase, creature_name, creature_hunger_limit, plant_death_time, plant_harvest_time,plot_id, creature_harvest_phase, plant_till_growth
				FROM 
					((public.hm_plant 
				INNER JOIN
				    hm_creature 
				ON hm_plant.plant_creature_id = hm_creature.creature_id) INNER JOIN hm_plot ON plant_id = plot_plant_id)
				WHERE 
					plant_id= ?';

		$st = $this->app['pdo']->prepare($query);
		$st->bindParam(1, $id, PDO::PARAM_INT);
		$st->execute();	
	
		$data = array();
		$i = 0;
		if($row = $st->fetch(PDO::FETCH_ASSOC)){
			$data['plant_id'] = $row['plant_id'];
			$data['plant_phase'] = $row['plant_phase'];
			$data['plot_id'] = $row['plot_id'];
			$data['creature_name'] = $row['creature_name'];
			$data['creature_hunger_limit'] = $row['creature_hunger_limit' ];
			$data['plant_death_time'] = $row['plant_death_time'];
			$data['plant_harvest_time'] = $row['plant_harvest_time'];
			$data['creature_harvest_phase'] = $row['creature_harvest_phase'];
			$data['plant_till_growth'] = $row['plant_till_growth'];
		}
		return $data;
	}

	public function getAllAnimalInfo(){

	}

	public function getAnimalInfoByID($id){

	}

	/**UNITY API**/
	public function getAllPlantDetailedInfo($id){
		$query = "	SELECT 
						plot_id, plant_id, plant_creature_id, plant_death_time, plant_till_growth, plant_harvest_time, plant_harvest_limit, plant_phase, creature_name, plant_death 
					FROM 
						(public.hm_plant 
					INNER JOIN
					    hm_creature 
					ON hm_plant.plant_creature_id = hm_creature.creature_id) RIGHT OUTER JOIN hm_plot ON plant_id = plot_plant_id
					WHERE
						user_id = {$id}
                     ORDER BY plot_id ASC;
					";
		$st = $this->app['pdo']->prepare($query);
		$st->execute();

		$data = array();
		$i = 0;
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			$data[$i]['plot_id'] = $row['plot_id'];
			
			
			if($row['plant_id'] != null){
				$data[$i]['plant_id'] = $row['plant_id'];
				$data[$i]['plant_creature_id'] = $row['plant_creature_id'];
				$data[$i]['plant_death_time'] = $row['plant_death_time'];
				$data[$i]['plant_till_growth'] = $row['plant_till_growth'];
				if($row['plant_harvest_time'] != null){
					$data[$i]['plant_harvest_time'] = $row['plant_harvest_time'];
				}else{
					$data[$i]['plant_harvest_time'] = "";
				}
				$data[$i]['plant_harvest_limit'] = $row['plant_harvest_limit'];
				$data[$i]['plant_phase'] = $row['plant_phase'];
				$data[$i]['creature_name'] = $row['creature_name'];
				if($row['plant_death'] != null){
					$data[$i]['plant_death'] = $row['plant_death'];
				}else{
					$data[$i]['plant_death'] = false;
				}
			}else{
				$data[$i]['plant_id'] = -1;
				$data[$i]['plant_death_time'] = "";
				$data[$i]['plant_till_growth'] = "";
				$data[$i]['plant_harvest_time'] = "";
				$data[$i]['plant_harvest_limit'] =-1;
				$data[$i]['plant_phase'] = -1;
				$data[$i]['creature_name'] = "";
				$data[$i]['plant_death'] = false;
                $data[$i]['plant_creature_id'] = -1;
			}
			
			
			
			$i++;
		}
		return $data;
	}

	public function getCreatureData($request){
		$query ='SELECT 
					*
				FROM 
					hm_creature
				';

		if($request == 0){ //get Plant Data
			$query .= 'WHERE creature_type = false';
		}else if($request == 1){ //get Animal Data
			$query .= 'WHERE creature_type = true';
		}
		//else get All data
		$query .=" ORDER BY creature_id ASC";
		
		$st = $this->app['pdo']->prepare($query);
		$st->execute();
		$data = array();
		$i = 0;
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			$data[$i]['creature_id'] = $row['creature_id'];
			$data[$i]['creature_name'] = $row['creature_name'];
			$data[$i]['creature_hunger_limit'] = $row['creature_hunger_limit'];
			$data[$i]['creature_harvest_phase'] = $row['creature_harvest_phase'];
			$data[$i]['creature_max_phase'] = $row['creature_max_phase'];
			$data[$i]['creature_growth_time'] = $row['creature_growth_time'];
			$data[$i]['creature_type'] = $row['creature_type'];
			$i++;
		}

		return $data;
	}

	private function getPlantMaxIdx(){
		$query = "SELECT MAX(plant_id) as index FROM hm_plant";
		$st = $this->app['pdo']->prepare($query);
		$st->execute();
		$plant_id = -1;
		
		if($row = $st->fetch(PDO::FETCH_ASSOC)){
			$plant_id = $row['index'];
		}

		return $plant_id;
	}

	private function processPlantJson($PlantData){
		$param = array();
		$param[1] = $PlantData['plant_creature_id'];
		$param[2] = $PlantData['plant_birth_time'];
		$param[3] = $PlantData['plant_phase'];
		$param[4] = $PlantData['plant_harvest_limit'];
		$param[5] = $PlantData['plant_death'];
		$param[6] = $PlantData['plant_till_growth'];
		$param[7] = $PlantData['plant_death_time'];

		return $param;
	}

	private function fillPlot($plot_id, $plant_id){
		try{
			$query = "UPDATE public.hm_plot
					SET plot_plant_id=?
					WHERE plot_id = ?;";
			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $plant_id, PDO::PARAM_INT);
			$st->bindParam(2, $plot_id, PDO::PARAM_INT);

			$st->execute();
		}catch(Exception $ex){
			throw $ex;
		}
	}

	private function create_plant($jsonString, $plot_id, $plant_id){
		try{
			$PlantData = json_decode($jsonString,true);
			$query = 	"INSERT INTO public.hm_plant(plant_creature_id, plant_birth_time, plant_harvest_time, plant_phase, plant_harvest_limit, plant_death, 			plant_till_growth, plant_death_time, plant_id)
						VALUES (?, ?, null, ?, ?, ?, ?, ?, {$plant_id});";

			$param = $this->processPlantJson($PlantData);
			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $param[1], PDO::PARAM_INT);
			$st->bindParam(2, $param[2], PDO::PARAM_STR);
			$st->bindParam(3, $param[3], PDO::PARAM_INT);
			$st->bindParam(4, $param[4], PDO::PARAM_INT);
			$st->bindParam(5, $param[5], PDO::PARAM_BOOL);
			$st->bindParam(6, $param[6], PDO::PARAM_STR);
			$st->bindParam(7, $param[7], PDO::PARAM_STR);
			$st->execute();
		}catch(Exception $ex){
			throw $ex;
		}
	}

	public function plantingNewPlant($jsonString, $plot_id){
		try{
			$plant_id = $this->getPlantMaxIdx()+1;
			if($plant_id <= 0){
				return "Error: Plant Index Cannot be received from Database";
			}
			
			$this->create_plant($jsonString, $plot_id, $plant_id);
			$this->fillPlot($plot_id, $plant_id);

			return $plant_id;
		}catch(Exception $ex){
			throw $ex;
		}

	}

	public function updatePlantDeath($plant_id, $death){
		$query = "UPDATE 
					public.hm_plant
				SET 
					plant_death = ?
				WHERE 
					plant_id = ?;";
		$st = $this->app['pdo']->prepare($query);
		$st->bindParam(1, $death, PDO::PARAM_BOOL);
		$st->bindParam(2, $plant_id, PDO::PARAM_INT);

		if($st->execute()){
			return "Success";
		}else{
			return $st->errorCode();
		}
	}

	public function updatePlantPhase($plant_id, $plant_till_growth, $phase){
		$query = "UPDATE 
					public.hm_plant
				SET 
					plant_till_growth = ?, plant_phase = ?
				WHERE 
					plant_id = ?;";
		$st = $this->app['pdo']->prepare($query);
		$st->bindParam(1, $plant_till_growth, PDO::PARAM_STR);
		$st->bindParam(2, $phase, PDO::PARAM_INT);
		$st->bindParam(3, $plant_id, PDO::PARAM_INT);

		if($st->execute()){
			return "Success";
		}else{
			return $st->errorCode();
		}
	}

	public function updatePlantLastPhase($plant_id, $plant_harvest_time, $phase){
		$query = "UPDATE 
					public.hm_plant
				SET 
					plant_harvest_time = ?, plant_phase = ?
				WHERE 
					plant_id = ?;";
		$st = $this->app['pdo']->prepare($query);
		$st->bindParam(1, $plant_harvest_time, PDO::PARAM_STR);
		$st->bindParam(2, $phase, PDO::PARAM_INT);
		$st->bindParam(3, $plant_id, PDO::PARAM_INT);

		if($st->execute()){
			return "Success";
		}else{
			return $st->errorCode();
		}
	}

	public function updatePlantHarvest($harvest_limit, $harvest_time, $plant_id, $creature_id){
		//masukkan kedalam inventory

		//ubah harvest limit dan harvest time
		$query = "UPDATE 
					public.hm_plant
				SET 
					plant_harvest_time = ?, plant_harvest_limit = ?
				WHERE 
					plant_id = ?;";
		$st = $this->app['pdo']->prepare($query);
		$st->bindParam(1, $harvest_time, PDO::PARAM_STR);
		$st->bindParam(2, $harvest_limit, PDO::PARAM_INT);
		$st->bindParam(3, $plant_id, PDO::PARAM_INT);

		if($st->execute()){
			return "Success";
		}else{
			return $st->errorCode();
		}
	}

	public function updatePlantDeathTime($deathtime, $plant_id){
		try{
			$query = "UPDATE 
						public.hm_plant
					SET 
						plant_death_time = ?
					WHERE 
						plant_id = ?;";
			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $deathtime, PDO::PARAM_STR);
			$st->bindParam(2, $plant_id, PDO::PARAM_INT);
		}catch(Exception $ex){
			throw $ex;			
		}
	}

	public function emptyPlot($plot_id){
		try{
			$query = 	"UPDATE 
							public.hm_plot
						SET 
							plot_plant_id=null 
						WHERE 
							plot_id = ?;";

			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $plot_id, PDO::PARAM_INT);
			
			$st->execute();
		}catch(Exception $ex){
			throw $ex;
		}
		
	}

	public function deletePlant($plant_id){
		try{
			$query = 	"DELETE FROM 
							public.hm_plant
						WHERE 
							plant_id = ? ;";

			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $plant_id, PDO::PARAM_INT);
			$st->execute();
		}catch(Exception $ex){
			throw $ex;
		}
			
	}

	public function destroyPlotPlant($plant_id, $plot_id){
		//update plant_id in plot id = null;
		try{
			$this->emptyPlot($plot_id);
			$this->deletePlant($plant_id);
		}catch(Exception $ex){
			throw $ex;
		}
		
	}

	public function levelUp($level, $max_stamina, $stamina, $exp, $next_exp, $character_id){
		try{
			$query = "	UPDATE 
							public.hm_maincharacter
						SET 
							character_stamina= ?, character_max_stamina=?, character_level=?, character_exp=?, character_next_exp=?
						WHERE 
							character_id = ?;";
			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $stamina, PDO::PARAM_INT);
			$st->bindParam(2, $max_stamina, PDO::PARAM_INT);
			$st->bindParam(3, $level, PDO::PARAM_INT);
			$st->bindParam(4, $exp, PDO::PARAM_INT);
			$st->bindParam(5, $next_exp, PDO::PARAM_INT);
			$st->bindParam(6, $character_id, PDO::PARAM_INT);
			
			$st->execute();
		}catch(Exception $ex){
			throw $ex;
		}
	}

	public function updatestats($exp, $stamina, $character_id){
		try{
			$query = "	UPDATE 
							public.hm_maincharacter
						SET 
							character_stamina= ?, character_exp=?
						WHERE 
							character_id = ?;";
			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $stamina, PDO::PARAM_INT);
			$st->bindParam(2, $exp, PDO::PARAM_INT);
			$st->bindParam(3, $character_id, PDO::PARAM_INT);
			
			$st->execute();
		}catch(Exception $ex){
			throw $ex;
		}
	}



	public function recoverStamina($stamina, $recoverStamina, $character_id){
		try{
			$query = "	UPDATE 
							public.hm_maincharacter
						SET 
							character_stamina= ?, character_stamina_recover= ?
						WHERE 
							character_id = ?;";
			$st = $this->app['pdo']->prepare($query);
			$st->bindParam(1, $stamina, PDO::PARAM_INT);
			$st->bindParam(2, $recoverStamina, PDO::PARAM_STR);
			$st->bindParam(3, $character_id, PDO::PARAM_INT);
			$st->execute();
			return "Success";
		}catch(Exception $ex){
			throw $ex;
		}
	}

	public function getCountPlant(){
		$st = $this->app['pdo']->prepare("SELECT COUNT(*) as counted FROM hm_plant;");
		$st->execute();

		$data = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			array_push($data, $row['counted']);
		}

		return $data;
	}


	public function getCountItem($inventory_id){
		$st = $this->app['pdo']->prepare("SELECT total_item as counted FROM hm_itemtoinventory WHERE inventory_id = {$inventory_id};");
		$st->execute();

		$data = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			array_push($data, $row['counted']);
		}

		return $data;
	}


	public function getCountAnimal(){
		$st = $this->app['pdo']->prepare("SELECT COUNT(*) as counted FROM hm_animal;");
		$st->execute();
		
		$data = array();
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			array_push($data, $row['counted']);
		}

		return $data;
	}
    
    private function getCharMaxIdx(){
		$query = "SELECT MAX(character_id) as index FROM hm_maincharacter;";
		$st = $this->app['pdo']->prepare($query);
		$st->execute();
		$char_id = -1;
		
		if($row = $st->fetch(PDO::FETCH_ASSOC)){
			$char_id = $row['index'];
		}

		return $char_id;
	}

	private function getInventory(){
		try{
			$query = "SELECT MAX(inventory_id) as index FROM hm_inventory;";
			$st = $this->app['pdo']->prepare($query);
			$st->execute();

			$inventory_id = 0;

			if($row = $st->fetch(PDO::FETCH_ASSOC)){
				$inventory_id = $row['index']+1;
			}
			$gift_box_id = $inventory_id+1;
			$query = "INSERT INTO public.hm_inventory(inventory_max_slot, inventory_id)
						VALUES(10, {$inventory_id}), (10,{$gift_box_id})";

			$st = $this->app['pdo']->prepare($query);
			$st->execute();

			return $inventory_id;
		}catch(Exception $ex){
			throw $ex;
		}
	}

	private function InsertMainCharacter($char_id, $inventory_id){
		try{
			$id = $this->getCharMaxIdx()+1; 
			$gift_box_id = $inventory_id+1;
			$query = 	"INSERT INTO public.hm_maincharacter(character_name, character_stamina, inventory_id, character_money, character_max_stamina, character_level, character_exp, character_next_exp, gift_box_id, character_id, character_stamina_recover, character_firebase_id)
						VALUES ('player-{$char_id}', 250, {$inventory_id}, 0, 250, 0, 0, 100, {$gift_box_id}, {$id}, NULL, '{$char_id}');";

			$st = $this->app['pdo']->prepare($query);
			$st->execute();
            
            return $id; 
		}catch(Exception $ex){
			throw $ex;
		}
	}
    
    private function InsertMainCharacterByName($char_id, $inventory_id, $name){
		try{
			$id = $this->getCharMaxIdx()+1; 
			$gift_box_id = $inventory_id+1;
			$query = 	"INSERT INTO public.hm_maincharacter(character_name, character_stamina, inventory_id, character_money, character_max_stamina, character_level, character_exp, character_next_exp, gift_box_id, character_id, character_stamina_recover, character_firebase_id)
						VALUES ('{$name}', 250, {$inventory_id}, 0, 250, 0, 0, 100, {$gift_box_id}, {$id}, NULL, '{$char_id}');";

			$st = $this->app['pdo']->prepare($query);
			$st->execute();
            
            return $id; 
		}catch(Exception $ex){
			throw $ex;
		}
	}

	private function CharacterTrigger($char_id, $inventory_id){
		try{
			$query =   "INSERT INTO public.hm_plot(
								plot_plant_id, user_id)
						VALUES 	(NULL, {$char_id}),(NULL, {$char_id}),(NULL, {$char_id}),
								(NULL, {$char_id}),(NULL, {$char_id}),(NULL, {$char_id}),
								(NULL, {$char_id}),(NULL, {$char_id}),(NULL, {$char_id});";

			$st = $this->app['pdo']->prepare($query);
			$st->execute();
			
			$queryinventory = "INSERT INTO public.hm_itemtoinventory(
								inventory_id,item_id,total_item)
							   VALUES ({$inventory_id},1,0),({$inventory_id},2,0),({$inventory_id},3,0),
							   		  ({$inventory_id},4,0),({$inventory_id},5,0),({$inventory_id},6,0),
							   		  ({$inventory_id},7,0),({$inventory_id},8,0);";

			$stinventory = $this->app['pdo']->prepare($queryinventory);
			$stinventory->execute();			
		}catch(Exception $ex){
			throw $ex;
		}
	}
    
    public function createCharacter($char_id) {
        try{
        	$inventory_id = $this->getInventory();
        	$player_id = $this->InsertMainCharacter($char_id, $inventory_id);
        	$this->CharacterTrigger($player_id, $inventory_id);
            
            return $player_id; 
		}catch(Exception $ex){
			throw $ex;
		}
    }

    public function createCharacterByName($char_id, $name) {
        try{
        	$inventory_id = $this->getInventory();
        	$player_id = $this->InsertMainCharacterByName($char_id, $inventory_id, $name);
        	$this->CharacterTrigger($player_id, $inventory_id);
            
            return $player_id; 
		}catch(Exception $ex){
			throw $ex;
		}
    }		

    public function getAllItem(){
    	try{
    		$query = "SELECT * FROM hm_item;";
    		$st = $this->app['pdo']->prepare($query);
			$st->execute();
			$data = array();
		$i = 0;	
		while($row = $st->fetch(PDO::FETCH_ASSOC)){
			$data[$i]['item_type'] = $row['item_type'];
			
			$data[$i]['item_name'] = $row['item_name'];
			$data[$i]['item_desc'] = $row['item_value'];
			$data[$i]['item_value'] = $row['item_value'];
			$data[$i]['item_id'] = $row['item_id'];
			$data[$i]['creature_id'] = $row['creature_id'];
			$i++;
		}
			return $data;	
    	}catch(Exception $ex){
    		throw $ex;
    	}
    }
}

?>