#!/usr/bin/php
<?php
date_default_timezone_set("Europe/Rome");

$a = "questa è una frase";
$b = "anche questa lo è";
$c = "perepe perepe perepe";

$ha = hash("sha256",$a);
$hb = hash("sha256",$b.$ha);

echo $ha."\n";
echo $hb."\n";

class Block implements JsonSerializable
{
	public $hash;
	public $previousHash; 
	private $data; //our data will be a simple message.
	private $timeStamp; //as number of milliseconds since 1/1/1970.
	private $nonce;

	//Block Constructor.  
	public function __construct($data,$previousHash) 
	{
	  if (($data == "") and ($previousHash == 0)) return;
		$this->data = $data;
		$this->previousHash = $previousHash;
		$this->timeStamp = time();
//		$this->hash = $this->calculateHash(); //Making sure we do this after we set the other values.
	}

	//Calculate new hash based on blocks contents
	public function calculateHash() 
	{
		$calculatedhash = hash("sha256",$this->previousHash.$this->timeStamp.$this->nonce.$this->data);
		return $calculatedhash;
	}

	public function show() 
	{
	  echo "data:   ".$this->data."\n";
	  echo "pHash:  ".$this->previousHash."\n";
	  echo "tStamp: ".$this->timeStamp."\n";
	  echo "hash:   ".$this->hash."\n";
	  echo "nonce:  ".$this->nonce."\n";
	}
	
	public function jsonSerialize() 
	{
	  return [  "data" => $this->data,
	            "previousHash" => $this->previousHash,
	            "timeStamp" => $this->timeStamp,
	            "hash" => $this->hash,
	            "nonce" => $this->nonce
	         ];
	}
	
	public function setAll($s)
	{
	  $this->data = $s["data"];
	  $this->previousHash = $s["previousHash"];
	  $this->timeStamp = $s["timeStamp"];
	  $this->hash = $s["hash"];
	  $this->nonce = $s["nonce"];
	}
	
	//Increases nonce value until hash target is reached.
	public function mine($difficulty) 
	{
	  $start=microtime(true);
	  $this->nonce=0;
		$target = str_repeat("0",$difficulty); //Create a string with difficulty * "0" 
//		echo("target : " . $target . " - ");
		while (substr($this->hash, 0, $difficulty) !== $target) 
		{
//		  echo $this->nonce.": ".substr($this->hash, 0, $difficulty)."; ";
			$this->nonce++;
			$this->hash = $this->calculateHash();
		}
		$time=round((microtime(true)-$start),3);
		echo ("\nBlock Mined!!!\n" . "   nonce: ".$this->nonce." hash: ".$this->hash." t:".$time."s\n");
	}
	
}

class noobChain 
{
  public $BlockChain = array();
  public $Difficulty = 5;
  
  public function __construct()
  {
    echo "noobChain created.\n";
  }
  
  public function __destruct()
  {
    echo "noobChain destructed.\n";
  }
  
	public function addBlock($data) 
	{
	  if (count($this->BlockChain) == 0) $previousBlock="0";
	  else $previousBlock=$this->BlockChain[count($this->BlockChain)-1]->hash;
	  $block = new Block($data,$previousBlock);
		$block->mine($this->Difficulty);
		$this->BlockChain[]=$block;
	}
	

	public function isValid() 
	{
		$hashTarget = str_repeat("0",$this->Difficulty);
		
		//loop through blockchain to check hashes:
		for($i=1; $i < count($this->BlockChain); $i++) 
		{
			$currentBlock = $this->BlockChain[$i];
			$previousBlock = $this->BlockChain[$i-1];
			//compare registered hash and calculated hash:
			if ($currentBlock->hash !== $currentBlock->calculateHash())
			{
				echo ("Current Hashes not equal\n");			
				return false;
			}
			//compare previous hash and registered previous hash
			if ($previousBlock->hash !== $currentBlock->previousHash) 
			{
				echo ("Previous Hashes not equal\n");
				return false;
			}
			//check if hash is solved
			if (substr($currentBlock->hash, 0, $this->Difficulty) !== $hashTarget) 
			{
				echo ("This block hasn't been mined\n");
				return false;
			}
		}
		return true;
}

  public function save($pn) // pathname
  {
    $json=json_encode($this->BlockChain,JSON_PRETTY_PRINT);
    file_put_contents($pn,$json);
  }
  
  public function getFromFile($pn) // pathname
  {
    $start=microtime(true);
    $f = json_decode(file_get_contents($pn),true);
    foreach ($f as $i => $block)
    {
      $Block = new Block("",0);
      $Block->setAll($block);
      $this->BlockChain[]=$Block;
    }
		$time=round((microtime(true)-$start)*1000.0,3);
		echo __FUNCTION__.": $time"."ms\n";
  }
  
}

$start=microtime(true);
$bc = new noobChain();
$bc->addBlock($a);
$bc->addBlock($b);
$bc->addBlock($c);

echo "BlockChain is ";
if ($bc->isValid()) echo "Ok.\n";
else echo "Ko.\n";

foreach ($bc->BlockChain as $i => $v) 
{
  echo "Block ".$i."\n";
  $v->show();
}  
$bc->save("bc.txt");

$bc=null;
$bc = new noobChain();
$bc->getFromFile("bc.txt");
echo "BlockChain is ";
if ($bc->isValid()) echo "Ok.\n";
else echo "Ko.\n";
foreach ($bc->BlockChain as $i => $v) 
{
  echo "Block ".$i."\n";
  $v->show();
}  
$time=round((microtime(true)-$start),3);
echo "Total execution time: $time"."s\n";

