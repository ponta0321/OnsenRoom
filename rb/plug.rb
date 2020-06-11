#!/bin/ruby -Ku

print("Content-Type: text/plain;charset=utf-8\n\n")
require 'json.rb'
require 'cgi.rb'
cgi=CGI.new
flag="0" #0=roll 1=descript
bclocation='../BCDice/src'
dicebot_name='DiceBot'
dicebot_command=''
if !cgi['flag'].nil? && !cgi['flag'].empty? then
	flag=cgi['flag']
end
if !cgi['bclocation'].nil? && !cgi['bclocation'].empty? then
	bclocation=cgi['bclocation']+'src'
end
if !File.exist?(bclocation) then
	json_array={
	  'error'=>'bac_location_error'
	}
	puts json_array.to_json
	exit
end
$LOAD_PATH<<bclocation
if !cgi['dicebot_name'].nil? && !cgi['dicebot_name'].empty? then
	dicebot_name=cgi['dicebot_name']
end
if !cgi['dicebot_command'].nil? && !cgi['dicebot_command'].empty? then
	dicebot_command=cgi['dicebot_command']
end
if flag !="1" && dicebot_command=='' then
	json_array={
	  'error'=>'not_exist_command'
	}
	puts json_array.to_json
	exit
end
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
  def gotHelpMessage
    return @diceBot.getHelpMessage
  end
end
bcdiceMarker=OnsenBCDiceMaker.new
bcdice=bcdiceMarker.newBcDice()
bcdice.setCollectRandResult(true);
if File.exist?(bclocation+'/extratables') then
    bcdice.setDir(bclocation+'/extratables',dicebot_name)
end
bcdice.setGameByTitle(dicebot_name)
if flag !="1" then
	bcdice.setMessage(dicebot_command)
	gmsg=bcdice.getMessage
	res_msg,secret_flag=bcdice.dice_command
	res_rand=bcdice.getRandResults
	json_array={
	  'secret_flag'=>secret_flag,
	  'gmsg'=>gmsg,
	  'res_rand'=>res_rand,
	  'res_msg'=>res_msg
	}
else 
	ght=bcdice.gotHelpMessage
	json_array={
	  'ght'=>ght
	}
end
puts json_array.to_json