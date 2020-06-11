$KCODE='UTF8';
require 'json.rb'
bclocation='../BCDice/src'
dicebot_name='DiceBot'
dicebot_command=''
if !ARGV[0].nil? && !ARGV[0].empty?
	bclocation=ARGV[0]+'src'
end
if !File.exist?(bclocation)
	json_array={
	  'error'=>'bac_location_error'
	}
	puts json_array.to_json
	exit
end
if !ARGV[1].nil? && !ARGV[1].empty?
	dicebot_name=ARGV[1]
end
if !ARGV[2].nil? && !ARGV[2].empty?
	dicebot_command=ARGV[2]
end
$LOAD_PATH<<bclocation
require 'bcdiceCore.rb'
require 'configBcDice.rb'
class OnsenBCDiceMaker<BCDiceMaker
  def newBcDice
    bcdice=OnsenBCDice.new(self,@cardTrader,@diceBot,@counterInfos,@tableFileData)
    return bcdice
  end
end
class OnsenBCDice<BCDice
  def getMessage
    return @message
  end
end
bcdiceMarker=OnsenBCDiceMaker.new
bcdice=bcdiceMarker.newBcDice()
bcdice.setCollectRandResult(true);
if File.exist?(bclocation+'/extratables')
    bcdice.setDir(bclocation+'/extratables',dicebot_name)
end
bcdice.setGameByTitle(dicebot_name)
bcdice.setMessage(dicebot_command)
gmsg=bcdice.getMessage
res_msg,secret_flag=bcdice.dice_command
res_rand=bcdice.getRandResults
#print(secret_flag,'|',gmsg,'|',"#{res_rand}",'|',res_msg)
json_array={
  'secret_flag'=>secret_flag,
  'gmsg'=>gmsg,
  'res_rand'=>res_rand,
  'res_msg'=>res_msg
}
puts json_array.to_json