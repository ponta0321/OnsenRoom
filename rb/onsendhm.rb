$KCODE='UTF8';
require 'json.rb'
bclocation='../BCDice/src'
dicebot_name='DiceBot'
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
  def gotHelpMessage
    return @diceBot.getHelpMessage
  end
end
bcdiceMarker=OnsenBCDiceMaker.new
bcdice=bcdiceMarker.newBcDice()
if File.exist?(bclocation+'/extratables')
    bcdice.setDir(bclocation+'/extratables',dicebot_name)
end
bcdice.setGameByTitle(dicebot_name)
ght=bcdice.gotHelpMessage
json_array={
  'ght'=>ght
}
puts json_array.to_json