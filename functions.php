<?php
require_once 'db_vars.php';
function GetExpToNextLevel($thislevel)
{

	if($thislevel == 1)
		return 5;
	else if($thislevel == 0)
		return 0;
	else
	{
		$result = (((int)($thislevel / 4) * (int)($thislevel / 4)) + 5 + (($thislevel - 1) * 10)) + GetExpToNextLevel($thislevel - 1);
		return $result;
	}
}

function SanitizeString($string)
{
	$string = stripslashes($string);
	$string = htmlentities($string);
	$string = strip_tags($string);
	return $string;
}
//THANKS TO MATTHEW HAMILTON (MATT@BROKENDESTINY.COM) FOR THIS SCRIPT
function censor($text){
    $rop='*'; // change this to whatever you want! it's the masking. change it to a & and all your censored words will come out like f&&&
        for ($i=1;$i<strlen($text);$i++){
            $replace .= $rop;
        }
        $newText = substr_replace($text,  $replace,  1);
        return $newText;
    }
function GetAllyIncrement($allyLevel, $allyInc)
{
    if($allyLevel <= 1)
        return 0;
    else
    {
        $result = 2 * (($allyLevel - 1) * $allyInc) + GetAllyIncrement($allyLevel - 1, $allyInc);
        return $result;
    }
}

//these are from the facebook documentation
function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}

function GetMissionTitle($MissionNo)
{
$missionTitle[0] = "It's Tuesday; get through a 'normal' school day";
$missionTitle[1] = "Search for your missing friend";
$missionTitle[2] = "Participate in Thursday's swim meet";
$missionTitle[3] = "<i>Unknown name, unknown number;</i> answer a mysterious phone call";
$missionTitle[4] = "Call for help";
$missionTitle[5] = "Evade the armoured monstrosity";
$missionTitle[6] = "Escape the Vile Emperor";
$missionTitle[7] = "Reset the program and go through another Tuesday";
$missionTitle[8] = "Find clues to unravel the cause behind Shuzhue's disappearance";
$missionTitle[9] = "Terminate the program";
$missionTitle[10] = "Orient yourself and find shelter";
$missionTitle[11] = "Convince the Roughlanders to help you";
$missionTitle[12] = "Learn to ride a mount";
$missionTitle[13] = "Greet Krox, Head-taker of the First Spawn";
$missionTitle[14] = "Pass the Roughlander loyalty test";
$missionTitle[15] = "Question Binaris to learn the truth";
$missionTitle[16] = "Decipher the eight hundred year old message from Yuko Seig";
$missionTitle[17] = "Prove your status to the Roughlanders";
$missionTitle[18] = "Take a nap at the outpost";
$missionTitle[19] = "Warn the outpost of impending attack";
$missionTitle[20] = "Choose your weapon";
$missionTitle[21] = "Go see Binaris; she might prove useful";
$missionTitle[22] = "Organize the evacuation of the outpost";
$missionTitle[23] = "Flee into the Sand Lake";
$missionTitle[24] = "Cross the Great Sand Lake";
$missionTitle[25] = "The evil you know; choose your path";
$missionTitle[26] = "Navigate the mountain pass";
$missionTitle[27] = "\"Let's go, Chasm!\"";
$missionTitle[28] = "Investigate the Tunnel";
$missionTitle[29] = "The evil you don't know";
$missionTitle[30] = "Escape the Tunnel's deadly guardian";
$missionTitle[31] = "Identify the missing Roughlander";
$missionTitle[32] = "\"Haven't ye ever heard of a Flaqqer before?\"";
$missionTitle[33] = "Forage and collect water";
$missionTitle[34] = "Keep the monsters at bay";
$missionTitle[35] = "Head into the Jungle";
$missionTitle[36] = "Defend the Roughlanders";
$missionTitle[37] = "Tame the wild beast";
$missionTitle[38] = "Greet the sun";
$missionTitle[39] = "Meet little Lilyth and convince her to help you";
$missionTitle[40] = "Wander the abandoned city";
$missionTitle[41] = "Solve the conservatory puzzle";
$missionTitle[42] = "Select the correct lever";
$missionTitle[43] = "Explore the Temple of Sapphire";
$missionTitle[44] = "Supply power to the system";
$missionTitle[45] = "Reconstruct the past";
$missionTitle[46] = "Cryogenics?";
$missionTitle[47] = "Meet the Knights of Sapphiros";
$missionTitle[48] = "Speak with Yuko Seig";
$missionTitle[49] = "Train with Sir Sabien and the other Knights";
$missionTitle[50] = "The Deathsquads are coming; plan your next move";
$missionTitle[51] = "Rally the Roughlanders to your cause";
$missionTitle[52] = "Lead your caravan through the tunnel passage";
$missionTitle[53] = "Meet 'Big Daddy'";
$missionTitle[54] = "Make a quick exit";
$missionTitle[55] = "Outsmart the sand crawlers";
$missionTitle[56] = "Bargain for safe passage to Taiyou";
$missionTitle[57] = "Greet Lord Hex";
$missionTitle[58] = "Taiyou is said to be well guarded; scout ahead";
$missionTitle[59] = "Evade the Deathsquad patrol";
$missionTitle[60] = "Board the ferry into Taiyou";
$missionTitle[61] = "Give up your weapons";
$missionTitle[62] = "Visit the Roughlander Sanctioned outpost";
$missionTitle[63] = "Meet Tathos, the outpost leader";
$missionTitle[64] = "Do some digging; this place isn't all it seems";
$missionTitle[65] = "It's the food! Quick, do something!";
$missionTitle[66] = "Escape the sanctioned outpost";
$missionTitle[67] = "Take shelter in a nearby storehouse";
$missionTitle[68] = "Wait! You can't leave everyone there, can you?";
$missionTitle[69] = "Leave instructions with Masaru; he'll get the others out";
$missionTitle[70] = "You can't wait any longer; it's time to move on";
$missionTitle[71] = "See the sights of Taiyou";
$missionTitle[72] = "Tend to Aysel";
$missionTitle[73] = "Investigate the contaminated food and water";
$missionTitle[74] = "Oh no, what have you done?!";
$missionTitle[75] = "Meet up with friends";
$missionTitle[76] = "Help?";
$missionTitle[77] = "Deduce the solution";
$missionTitle[78] = "Salt! You need as much salt as possible, now!";
$missionTitle[79] = "You have to go back and set this right.";
$missionTitle[80] = "Administer the treatment you devised.";
$missionTitle[81] = "Follow Masaru's trail";
$missionTitle[82] = "Investigate the warehouse";
$missionTitle[83] = "Face down a Talon of the Vile Emperor";
$missionTitle[84] = "Return to the Sanctioned outpost";
$missionTitle[85] = "Discover the cure";
$missionTitle[86] = "Knight Masaru to save him";
$missionTitle[87] = "Watch the Knight Commander train a new Knight";
$missionTitle[88] = "Receive a startling invitation";
$missionTitle[89] = "March proudly through the streets of Taiyou";
$missionTitle[90] = "Battle your will against Fuzen's";
$missionTitle[91] = "Pass the Lord Regent's test";
$missionTitle[92] = "Meet the young Prince Narlhep";
$missionTitle[93] = "Learn of an assassination attempt";
$missionTitle[94] = "Put the pieces together";
$missionTitle[95] = "Flee the Oujou";
$missionTitle[96] = "Take the Prince to the Sanctioned outpost";
$missionTitle[97] = "March North to the Raman province";
$missionTitle[98] = "Meet up with the Lion Brigade";
$missionTitle[99] = "Onward to siege the Temple of Jade";

return $missionTitle[$MissionNo];
}

function GetMissionText($MissionNo)
{
$missionText[0] = "It's a Tuesday morning like every other except for the surprise science test and the constant feeling that someone is watching you. Wait - that stranger glaring at you from over there with the blonde, spiked hair and the strangely red-tinted eyes; do you know him from somewhere?";
$missionText[1] = "You swear she was here this morning, but your best friend, Shuzhue, didn't make an appearance in this afternoon's classes and now she's nowhere to be found after school. It's not like Shuzhue to not let you know if she's going to go home sick or something; maybe you should call her cell phone and make sure she's okay.";
$missionText[2] = "Your school president, Goji Nakamura, is hosting a swim meet after school on Thursday. Shuzhue still hasn't turned up, but you've been asked to fill in for her during the competition. Shuzhue's a pretty good swimmer; hopefully you can do half as well for the school's team. ";
$missionText[3] = "You stayed out late celebrating the swim team's victory, but now you're on your way home and it's close to midnight. You hear a familiar tune dancing out of the darkened entranceway to a nearby park - is that Shuzhue's ringtone? You should investigate; Shuzhue would do the same for you if your situations were reversed.";
$missionText[4] = "You found Shuzhue's cell phone in the sandbox of all places and you think you've also found a tattered piece of her school uniform. Some instinct alerts you to the danger of your situation and you look up only to find that you are surrounded by four intimidating strangers including the blonde spiky-haired individual from before. You'd better use that phone in your hand to call for help before whatever happened to Shuzhue happens to you too.";
$missionText[5] = "You begin to notice the oddness of the four people that have you surrounded; they each have glowing red eyes and matching ruby necklaces shaped like flame-topped diamonds. Unexpectedly, the four raise their arms and summon a surreal power, which encases you in a dome of red light. From the center of the dome, an unimaginable creature in spiked armour emerges to terrorize you - you'd better do something quick!";
$missionText[6] = "You've somehow managed to slow the thing down, but it doesn't look like that's going to be enough. Shuzhue's phone rings in your hands, startling you out of your terror, and instinctively, you put it to your ear. \"I can save you from the Vile Emperor,\" an enigmatic female voice states. \"You need only accept it and the power to escape him is yours.\"";
$missionText[7] = "You lived, but what an awful dream that was... Thankfully it wasn't real - or was it? By your reckoning, it should now be Friday, but it seems that it's Tuesday again. Okay, you know you've passed this science test before - what's going on?";
$missionText[8] = "The repeating Tuesdays are decidedly odd, but this gives you the opportunity to discover what happened to Shuzhue and prevent it, right? That's great in theory, but every time you think you get closer to the truth, the day resets on you and the phenomena is starting to get on your nerves...";
$missionText[9] = "You've managed to track Shuzhue's kidnappers to the roof of the hospital and now all that's left to do is to wait for them to strike. \"Reset the program...\" Where is that voice coming from? \"The Vile Emperor is coming; if we do not get them out in time the Chosen will die.\" Do they mean you, or maybe Shuzhue? Either way, you'd better convince them to shut off their 'program' before someone gets hurt.";
$missionText[10] = "The sun's heat is intense, baking your exposed skin, and your eyelids feel gritty as you force them open. Sand, sun, heat; you find yourself inexplicably in an open desert with nothing on your person. You don't know where you are or how you got here, but you'd better find shelter soon if you can.";
$missionText[11] = "For what it is worth, you discover that you are not alone out here. \"I'm Corpral Dahlia,\" the hardened red-headed woman introduces herself from the back of her alien-looking metal horse, \"and what would you be doin' out here in the Sand Lake all undressed as ye are?\"";
$missionText[12] = "Corporal Dahlia tosses you a cloth sack to cover yourself with and tells you that you'll be riding with her companion, Masaru. You eyes his metal horse dubiously, but Masaru gives you a friendly smile, \"Hop on, I'll show ye how it's done.\"";
$missionText[13] = "The mount's rump was an uncomfortable seat and you're glad when you finally arrive at what seems to be your destination. You dismount gratefully, only to find yourself face to face with a brutish-looking seven foot tall toad-person. \"I am Krox, Head-taker of the First Spawn. Who're you?\"";
$missionText[14] = "Your guides have identified themselves as Roughlanders and this tower-shaped building as their 'outpost.' You're still trying to absorb all this, when you're introduced to another, albeit smaller and less menacing, frog-person. \"I'm Ticket, the Croatin who's administering your loyalty test. Look at the screen and tell me what you see...\"";
$missionText[15] = "The Croatin's screen showed you some odd images, some you recognized and some you didn't, but in the end you were shown a picture of a room with a female robot and a mess of wires. \"Why would you see Binaris?\" Dahlia questions. \"Unless...\" She regards you with uncertainty. \"You'd better come with me. Binaris should be able to explain things better than I can.\"";
$missionText[16] = "Binaris was less than helpful, being an ancient program, but you've learned that you are the Chosen of a gem god and have been granted powers to face the Vile Emperor; that armoured menace that attacked you in the park. Also, the woman who saved you from him has left you a cryptic message from seemingly eight hundred years in the past.";
$missionText[17] = "Yuko Seig's message tells you to come to the Temple of Sapphire where she is supposed to be waiting for you. \"But Yuko Seig's dead,\" Masaru informs you. \"She made a pact with the Roughlanders eight hundred years ago for us to await the Chosen of Sapphiros and join with them against the Vile Emperor, but most of us think that's a myth now.\"";
$missionText[18] = "You've managed to trigger your strange new powers once again, though you're not quite sure how you did it. Either way, you've convinced the Roughlanders that you are who Binaris thinks you are and they've given you some clothes and a place to rest. Fittingly the sun is setting now, so you close your eyes, only to realize that it's not getting dark; the sun begins to rise again as soon as it touches the horizon!";
$missionText[19] = "Disoriented by the never-ending sunlight, you find resting difficult, so you take a tour of the outpost to familiarize yourself with your new surroundings. Up on the rooftop of the tall building you look out over the vast desert. Wait - what's that moving out there? Your power enables you to see further and you realize that it's an army headed this way!";
$missionText[20] = "You've alerted the Roughlanders to the danger approaching them. \"Those are the Deathsquad,\" Krox informs you, tossing you a bag of weapons to arm yourself with. You look over the mismatched collection and wonder if maybe your power could supply you with something better.";
$missionText[21] = "Yuko Seig instructed you to come to the Temple of Sapphire. That's all well and good, but you need to live to reach it. Questioning Binaris, you hope she can be of some help in defending the outpost.";
$missionText[22] = "You find Krox and Dahlia locked in an embrace and you wait awkwardly for them to finish. \"My crew and I will escort you to the Temple of Sapphire,\" Dahlia informs you. \"Krox and the other Croatins will be guarding our retreat.\"";
$missionText[23] = "With some Roughlanders to carry Binaris, Dahlia's crew as your escort, and the Croatin's to cover the retreat, everything seems all set, but all too soon the Deathsquad arrives and you realize that the Croatins are setting themselves up to be slaughtered on your behalf and there's nothing you can do to stop it.";
$missionText[24] = "You had to flee, but you hope Krox and the others will survive; they weren't so bad for alien frog-people. It's been a few bounces of the sun now since you left the outpost behind and began crossing the desert and, not being a Roughlander like the others with you, you are beginning to feel the effects of the intense heat.";
$missionText[25] = "Dahlia and a few Roughlanders headed out to scout and now only she's come back looking worse for wear. \"There's Deathsquad blocking the way. We can try to get by them, though I don't recommend it. The only other path left to us is the mountains, but they've got a bad reputation.\"";
$missionText[26] = "You chose the mountains and are glad that you did once you reach the much cooler and shaded mountain pass. Single file, you wind your way through the mountain trail as it leads upwards, when all of sudden your mount malfunctions, whining and bucking beneath you. \"Jump!\" Dahlia yells, noticing your distress.";
$missionText[27] = "You soon reach a gaping chasm that brings the whole group to a halt. After some deliberation it is decided that Ticket will attempt to leap the Chasm to bring a rope to the other side. Building his courage, the frog-person leaps, but falls just shy, grabbing hold of the rocky ledge. You'd better help him if you can!";
$missionText[28] = "You got Ticket across safely and everyone else followed him over on the length of rope tied at both ends. Now you find yourself in a dark tunnel that slopes downward. It's hard to see down here; maybe your power can help?";
$missionText[29] = "Masaru heads off to see what's ahead and Dahlia lets you know she's going to guard the rear in case the Deathsquad are following you. You stay with the others until you begin to hear a scuffle from the direction Dahlia went. Worried for Dahlia's safety, you decide it would be best if you went to investigate.";
$missionText[30] = "The glow of your arrow reveals the unspeakable horror of a skeletal face and a dismembered torso as an undead creature claws its way towards you in the darkness. You know that there is something you must do, but you find yourself paralyzed by fear. \"Don't just stand there!\" Dahlia commands. \"Run!\"";
$missionText[31] = "Dahlia tried to hold the creature off, but nothing seemed to slow it. Thankfully, warned by the scuffle, the Roughlanders have moved on and as you run through the tunnel you find it empty. There; the sun! You've found the exit, but near the opening you find an unconscious Roughlander who has been left behind by the others.";
$missionText[32] = "The sun seems to hold the creatures at bay and you find yourself with the rest of the Roughlanders outside on a rocky ledge overlooking a dense jungle. Thankfully the Roughlander that fell wasn't Masaru, but you find him badly injured and nursing his wounds nearby. You'd better see if there's something you can do for him.";
$missionText[33] = "The sunlight keeping everyone safe for now, you leave Masaru to get some rest in order to help the Roughlanders gather some water and forage for food. You climb down the rocky cliff to the jungle floor only to find that perhaps venturing into the unknown is not the best idea. \"Meep moop!\" An alien looking creature calls out to its prey before devouring it in one bite.";
$missionText[34] = "Just when you're starting to think that you're safe, the sun begins to dip below the mountains that surround you, filling the valley with long shadows. \"The sun,\" Dahlia informs you, \"without it, those creatures will be able to leave the tunnels!\" Is there something you can do to keep the creatures trapped inside?";
$missionText[35] = "There's nothing for it; you'll have to head into the jungle and search for a way out of this false paradise. Weapons out and packs on, the Roughlanders fan out in smaller groups. They look ready to defend themselves, but you remember how tough just one of the creatures was and you're worried about how Masaru will fair as weak as he still seems.";
$missionText[36] = "It's fully dark now; there is not an exit in sight and the things that are after you are relentless. Every able-bodied Roughlander has been fighting hard to stay alive, but the creatures just keep coming and now you are almost certain they have you surrounded.";
$missionText[37] = "It's pink and fleshy with rows upon rows of teeth and the meep-mooping animal from earlier is just within your reach. Desperate for any method of combating the deadly swarm of undead, you react on instinct and leap onto the creature's back. So far, so good; now how do you get this thing to listen to you?";
$missionText[38] = "The Meep-moop, as you've come to call it, was a big help in squashing some undead, but now that the fighting is over it has taken you away from the others and back to its lair. You climb down and say goodbye to your new friend before climbing your way out of its den and into the light of the sun once more.";
$missionText[39] = "You've found the others, but now there's a bigger problem. You're about to warn everyone about what you've discovered when suddenly there is a newcomer in your midst. \"Hi, I'm Lilyth,\" the little red-headed girl introduces herself. \"Who're you and how'd you get by my guardians?\"";
$missionText[40] = "After managing to put her at ease, little Lilyth offers to take you where you're going as long as you promise to come back and visit her sometime. She teleports you instantly to what you recognize as the ancient ruins of an image Binaris showed you of the Temple of Sapphire. Lilyth droops, her power exhausted for now, and she returns home, leaving you and the Roughlanders to explore. ";
$missionText[41] = "In the center of an open courtyard you find a small circular conservatory with a remarkable ceiling. As you turn the table below, the ceiling rotates to display different constellations on a night sky. Maybe if you spin them out in the right order, something will happen.";
$missionText[42] = "The table in the center rises to reveal five different coloured levers; each one labeled with a gem god's symbol. Presumably you need to pull a level to solve this puzzle and make the door to the temple open, but which one?";
$missionText[43] = "The door opens and you all rush inside to see what awaits you. It's cold in here and the walls are metal and soon the door shuts again, locking you within. The ancient building is lit poorly by flickering fluorescent lights. This is not at all what you expected to find, but since you're here now, you'd better take a look around.";
$missionText[44] = "You've found a computer bay with keyboards and computer terminals that you recognize as Earth's technology. Password: Sapphiros; that was easy. System power; 32%. You'll have to find a way to power the system if you want to know what it does.";
$missionText[45] = "You found your way to the main server, but this part of the temple is in really bad shape. You've had to traverse debris left from an ancient battle that took place here, but now you're in a room with the remains of a robot and a desiccated corpse that was never human. What happened here?";
$missionText[46] = "You've restored some power to the system by plugging Binaris in and you run back to the computer lab you were in before. Flipping through the menus, you find what the system is for. Cryogenics? You activate it, wondering what you're about to release from stasis.";
$missionText[47] = "\"I am Sir Sabien, Knight Commander of Sapphiros,\" the leader introduces himself. \"This is Jeth, Ris, Lady Adel and her squire Aysel. We've been waiting here for you at the request of Yuko Seig to help you in your mission and be your Knights.\"";
$missionText[48] = "\"Jeth, I'm sure the Chosen has many questions,\" Sir Sabien says. \"Why don't you go to the command center and set up a meeting with Yuko Seig.\" You give this Knight Commander a strange look; Yuko Seig is dead, isn't she? \"She is and she isn't,\" he answers cryptically. \"You'll see when you talk to her.\"";
$missionText[49] = "\"At first, strong emotion will trigger your powers, but later it will only inhibit them. I will teach you to control your powers,\" Sir Sabien informs you. \"There are three ways powers can manifest; physically, elementally and magically. Ris?\"";
$missionText[50] = "You've been training in the Temple for weeks and you're about as ready as you're going to be. Sabien and the others are concerned that the Deathsquads will eventually track you down. It's time to decide where to go next and get moving.";
$missionText[51] = "It's a long walk across the deserts to reach the fertile land of Taiyou and there is no sense in doing it without stopping. Besides, you feel you should warn the Roughlander outposts between here and Taiyou that the Vile Emperor may soon come for them, too. ";
$missionText[52] = "You've got quite the army behind you now. Whatever it is you intended, the Roughlanders see you as their 'Chosen One' and many have decided to follow you. The only trouble is that between here and the next outpost you must travel through a seldom-used tunnel that may prove to be more dangerous than even the predator-filled Sand Lakes. ";
$missionText[53] = "With your power and Sabien's help, you've made it through to the other side and a sprawling outpost community. Leaving Adel in charge, you take Masaru with you to meet with the outpost leader, a man named 'Big Daddy', and give him the same warning you've given at the other outposts you've passed.";
$missionText[54] = "As you head back to Adel and the others, Big Daddy announces your presence over loud speakers to the entire outpost. You think you see some Deathsquad soldiers patrolling this place and if you don't leave now, they'll likely try to stop you. You'd better rally those you brought with you and get them into the Sand Lake.";
$missionText[55] = "Quickly accepting a few new recruits from this outpost, you head west into another Sand Lake, only to see impossibly large creatures patrolling the sands in the distance. \"Those are sand crawlers,\" Razor, one of the newcomers informs you. \"We need a plan for how we're going to deal with them if they get any closer.\"";
$missionText[56] = "You've traveled nearly a full day unmolested, but just as you can see the last outpost before Taiyou up ahead, you're stopped by a contingent of Roughlanders on mounts and they don't look pleased to see an army marching toward them. \"Stop,\" their leader commands from a safe distance. \"Only five of you may come forward and know that this outpost is well defended.\"";
$missionText[57] = "With Masaru held hostage for your good behavior, you are led to the top floor of the outpost building, but there is no one there to greet you except for a deep voice over speakers. \"Ah, I have waited long to meet Chosen who stands against the Vile Emperor. I am Lord Hex and I am your ally.\"";
$missionText[58] = "Leaving the bulk of the Roughlanders behind at Hex's outpost, you decide to take a small party on with you to Taiyou. You don't know what awaits you there and you don't want to march an army on their borders and give the wrong impression. You take Razor with you to scout the way ahead.";
$missionText[59] = "There's a Deathsquad patrol before the bridge, which is the only way into Taiyou. \"Refugees still go into Taiyou,\" Razor tells you, \"and that Deathsquad encampment has been there for as long as I can remember.\" You have a thought about disguises for you and the Knights, but will it be enough to get you by?";
$missionText[60] = "You disguised yourself as a refugee seeking sanctuary in Taiyou to fool the Deathsquad soldiers, but the Knights refuse to remove their armour, even temporarily. \"I'll meet you in Taiyou,\" Sir Sabien announces as he leaves with Ris and Jeth.  \"I'll make my own way across,\" Adel states, \"don't worry about me just look after my sister, Aysel.\" ";
$missionText[61] = "Despite the best efforts of the Deathsquad patrol, everyone you brought with you makes it onto the ferry and eventually to the other side of the lake. Taiyoun soldiers, called Legionnaires, greet you there. \"Welcome to Taiyou, Roughlanders. You must leave your weapons at the gate.\"";
$missionText[62] = "You used your power to hide your bow and a few of the weapons your group brought with them; soon you are all led with the other Roughlander refugees to a fenced-in compound known as the Roughlander Sanctioned outpost. The small outpost building surrounded by a grassy treed garden with a fountain is not exactly what you were expecting.";
$missionText[63] = "This place doesn't exactly seem to be the center of all Roughlander culture like you were expecting, but it's time to locate the outpost leader. Maybe he or she will be able to explain things to you, or at the very least you can give him or her the same warning you gave the other outposts you visited on the way here.";
$missionText[64] = "\"Hi, I'm Tathos,\" seems to be all the leader of this place is able to say. Her attention span is limited and you get the impression that she's not all there.  Even worse, you begin to discover that most of the residents seem to have the same problem to varying degrees. What's going on here?";
$missionText[65] = "\"Aysel, there you are. I've been looking everywhere for you!\" Adel reprimands her sister. \"Come, we're leaving this place now. It's dangerous; I can feel it.\" \"Oh, must we?\" Aysel asks, taking a sip of the glass of water in her hand and developing a vacant expression that reminds you of all the others you've encountered here in the Sanctioned outpost.";
$missionText[66] = "\"I have a plan,\" you tell Adel, thinking furiously. There's a fence and guards, and you will certainly need your powers to help you escape the Sanctioned outpost before you're all overcome by the strange apathy that surrounds this place. \"Get ready to run when I say go.\"";
$missionText[67] = "\"Now, go!\" The fence around the outpost compound comes crashing down thanks to your power and at your command Adel starts to run beside you, dragging her sister by the arm. Outside, the mindless Roughlanders are confused by what you've done, but none of them take the opportunity to flee. Rushing past them, you make for the city buildings beyond.";
$missionText[68] = "You've found a Storehouse where you can take shelter for the time being, but once away from the compound, Aysel seems to stop struggling so much and fall ill. Was the food some kind of poison? If it is, you can't leave everyone else there to fall prey to it. You realize that you have to go back and see if there is anything you can do to fix this. ";
$missionText[69] = "Pocketing a hunk of cheese off of Aysel's plate, you realize you are no longer alone in the room. \"What are ye doin' there?\" Masaru asks and you think to take him with you back to Adel and Aysel when you take notice of the glass of water in his hand and the vacant expression on his face; he's been affected by the poison too, then.";
$missionText[70] = "You've been in the storehouse for a few days, but you're running low on food and water. You've also seen city guards patrolling the area and searching nearby buildings for escaped Roughlanders. You'd like to keep waiting for Masaru and others, but it's looking like you're going to have to face the facts and move on while you still can.";
$missionText[71] = "Once you get past the warehouse district you were taking shelter in, Taiyou seems... perfect. The people look healthy and happy and the beautiful city is decorated with green foliage. You try to keep to the less popular areas as you head northwards and further into Taiyou, but with Aysel as ill as she is, you're worried someone might take notice.";
$missionText[72] = "You're out of the city now, but you're not out of the woods yet. Aysel's condition has worsened and Adel is deeply concerned. You send the Knight out to find firewood to make a camp, so you can tend to Aysel without her looking over your shoulder.";
$missionText[73] = "Aysel is reacting as if she's taken some kind of poison, or maybe suffering from withdrawal from some kind of drug. There has to be a clue here somewhere; maybe it's in the food or water you brought from the Sanctioned outpost.";
$missionText[74] = "Pouring the water into an empty jar left over from your dwindling food supplies, you suspend the sample over the fire Adel has built and continue to examine it. Heat causes the particles in the water to multiply and agitate, until the water is boiling and has turned visibly green. The nature of the poison revealed, you suddenly have a vision of what might be happening to Masaru and the others.";
$missionText[75] = "You need help and you need it quickly, but there are few people in this place that you know you can trust. Where is Sir Sabien anyways? He, Ris and Jeth left you at the ferry docks with the promise to meet back up with you, but you've seen no sign of them yet. Perhaps it's time you used your powers to find out where they went.";
$missionText[76] = "You find Sir Sabien and the others and meet up with them. Ris immediately sees to Aysel, but after a moment she shakes her head and you know that there's nothing she can do against the poison in Aysel's system.  \"Welcome to the Raman Province,\" a riding patrol finds you, but doesn't seem to be hostile, yet. \"I'm Sir Leon Rama, how can I be of assistance?\"";
$missionText[77] = "Thanks to Sir Rama's offer of supplies, you now have a proper medic kit filled with unlabeled substances, along with the usual bandages and tools. As the Taiyoun medic looks over Aysel, under Adel's supervision, you take the contaminated water off a ways to perform a few tests.";
$missionText[78] = "You never thought the answer would come from Jeth, but he's right. Some sweat from your hands proves that salt can dissolve the green particles. If you can get enough salt together, you may have a chance of curing Aysel. But how can you get more salt?";
$missionText[79] = "Sir Rama returns with plenty of salt, more medical supplies, and uncontaminated food and water. You thank him profusely; armed with this, you can now go back to the Sanctioned outpost and try to set things right. Hopefully, you won't be too late.";
$missionText[80] = "The residents at the Sanctioned outpost are as mindless as ever, but Masaru and a few of the other Roughlanders that came with you to Taiyou are nowhere to be found. Arming the Knights with salt-water filled canteens, you instruct them to administer the treatment and take care of Aysel while you look for Masaru.";
$missionText[81] = "The note Masaru left for you is written in an alien language you don't recognize, but you assume that the unsteady writing and the blood smears on the page are not good signs. Either way, this note is proof that Masaru was here and has moved on. Now all you need is a way to track where he went.";
$missionText[82] = "The blood trail leads you to an abandoned-looking warehouse deeper in the city of Taiyou. Finding the door open a crack, you venture inside only to find that you're too late for the first Roughlander you come across. Next you find two more Roughlanders in really bad shape, but where is Masaru?";
$missionText[83] = "You've just found him atop a pile of crates where he'd been keeping lookout out a high window, when you hear footsteps entering the warehouse from the same entrance you used. Peering into the darkness, you realize that you have no hope of identifying who the newcomer is without help from your power. Closing your eyes, you try and see another way.";
$missionText[84] = "Lord Fuzen, Talon of the Vile Emperor, has caught sight of you in the dim light from the dirty window behind you. Katana in hand, he looks like he is prepared to leap the distance between you. If you want to save Masaru's life and your own, you'd better think fast!";
$missionText[85] = "Running faster than Fuzen's eyes can follow you, you make it back to the Sanctioned outpost, with Masaru kept safe by your power. You give Masaru the salt-water treatment after making him as comfortable as possible, but the odds seem to be against him recovering on his own. Maybe this place holds some answers?";
$missionText[86] = "Confused by what you've learned, you turn to Sir Sabien for answers. \"In my day, the Oujou were a powerful tribe of sorceresses from this area; very deadly. If they've cursed this place, the only way to reverse their magic would be to find the one responsible for it and undo the cause.\"";
$missionText[87] = "You Knighted Masaru to save his life and, like a miracle, it worked, putting Masaru back on his feet nearly instantaneously. Now your friend has powers of his own and Sir Sabien is determined to train him to use them on instinct, pushing the newly made Knight to his limit again and again.";
$missionText[88] = "Just when you think things have calmed down for the moment, a startling invitation arrives at the Sanctioned outpost for you. It's from the Lord Regent of Taiyou, formally requesting your presence at the palace for a meeting. Evidently, he knows you're here.";
$missionText[89] = "You delay your meeting with the regent in favour of getting to know Taiyou better. You learn about fatherless fifteen-year-old Prince Narlhep and his engagement to the regent's daughter, and that the Prince cannot be king until he turns sixteen. Armed with that knowledge, you prepare for your trip to the palace.";
$missionText[90] = "Lord Fuzen is waiting for you before the palace drawbridge with a sneer on his pointed features. \"Well, have you finally decided to crawl out from the rock you've been hiding under? Go home, Chosen, there is nothing in Taiyou for you.\"";
$missionText[91] = "Lord Viron, regent of Taiyou is a large and intimidating man and your meeting with him is mostly one-sided. He allows you to speak and says nothing, merely listening to your explanation of why you have come to Taiyou. \"I have heard you speak,\" he responds when you are finished. \"Now you will hear me speak.\"";
$missionText[92] = "After your meeting is over, Lord Viron leads you to a set of large double doors and admits you into the throne room. Prince Narlhep, a boy in an oversized suit of armour, is seated in his throne absently twirling a sword in his hands as if overcome with boredom. \"Who's this, Viron?\"";
$missionText[93] = "Instinctively, you try the doors and realize that Viron has sealed both you and the prince in the throne room. \"Oh, he's always doing that,\" Narlhep informs you. \"Ever since the assassination attempt he's been extra paranoid about my safety.\" Who would try to murder the prince?";
$missionText[94] = "\"The Oujou is bound to the crown,\" Prince Narlhep informs you. \"Only Lord Viron and I can command her, so I know I'm safe in her presence, because I can tell her what to do.\" Something bothers you about this statement and it only takes you a second to realize what it is. If the Oujou made an attempt on the Prince's life, then Viron must have ordered it.";
$missionText[95] = "Enraged, the Oujou tries to destroy you and the prince, fulfilling her orders to assassinate the would-be king. Desperately, you use your power to deflect her attack as you grab Prince Narlhep and flee in the first direction that presents itself.";
$missionText[96] = "Fleeing the Oujou, you use your power to carry Narlhep to the one safe place you can think of, the Roughlander Sanctioned outpost. Once there you find the Knights waiting for your return and you realize that since Narlhep is a descendant of the royal line, he might be able to break the Oujou curse on the fountain's water.";
$missionText[97] = "\"I'm not supposed to tell anyone about Taiyou's defenses, but if Lord Viron seeks to take over Taiyou, he'll be headed to the Temple of Jade to where the Weapon of Taiyou is located,\" Narlhep informs you. \"If we want to stop him, we'll have to go there, but the Leyins will be a problem.\"";
$missionText[98] = "You find yourself in the Raman province when familiar-looking soldiers surround you. You also recognize their leader; this is Sir Leon Rama with his Lion Brigade. \"Your Majesty, what brings you to the Raman province?\" Sir Rama asks. \"I trust that you are not being brought here against your will?\"";
$missionText[99] = "\"My men and I will accompany you, Prince Narlhep,\" Sir Rama declares. \"On behalf of my father, Lord Rama, the Lion Brigade is yours to command and we will see the traitorous Lord Viron removed from power.\" Your force now consists of the Lion Brigade, the Knights of Sapphiros and assorted Roughlanders as you continue your march to the Temple of Jade.";

return $missionText[$MissionNo];
}

function GetBronzeText($MissionNo)
{
$bronzeText[] = "Leaning on a tree in the schoolyard, his hateful expression is focused on you, but who is he?";
$bronzeText[] = "No answer... Hmm, that's odd; maybe her home phone?";
$bronzeText[] = "Hey, not bad, you came in third and your team won over all!";
$bronzeText[] = "The park is eerily lit by only one street lamp and it doesn't escape you that there is only one exit; still, this is your best friend we're talking about.";
$bronzeText[] = "\"911, emergency.\" You quickly describe your situation to the operator and give her the descriptions of the four around you.";
$bronzeText[] = "Somewhat foolishly, you charge the creature, but it ignores your pitiful attack and all you manage to do is injure yourself on an armoured spike. ";
$bronzeText[] = "None of this seems real, but before you the 'Vile Emperor' begins to form what appears to be lava between his hands and you can feel the heat of it.";
$bronzeText[] = "There's that blonde teenager again, but now that you see him for what he really is, should you follow him to see what he's up to?";
$bronzeText[] = "Early one Tuesday morning, you innocently question Shuzhue on her plans for the evening and learn that she's supposed to visit her grandmother in the hospital.";
$bronzeText[] = "The kidnappers are here! You notice the gleam of a sword before one of the four attacks you and you roll out of the way to save your own skin.";
$bronzeText[] = "You feel exposed, but there's no one here to see you as you dig yourself out of the sand and pick a direction to begin walking.";
$bronzeText[] = "You try to be circumspect in explaining your plight to these people, but it only makes them suspicious. \"Help doesn't come for free, ye know.\"";
$bronzeText[] = "\"This here's the throttle,\" Masaru announces and, before you know it, the metal horse rumbles to life beneath you and you're off, racing across the open sand.";
$bronzeText[] = "You stammer, unable to meet the creature's eyes, but you mumble your name afraid of what he might do if you don't answer him.";
$bronzeText[] = "The first picture that appears on the Croatin's screen is of Shuzhue  - how and why would they have a picture of her?";
$bronzeText[] = "\"There are five gem gods with Chosen represented on this planet,\" Binaris informs you. \"They are Sapphiros, Machalite, Rubia, Jedeite and Damos.\"";
$bronzeText[] = "\"I don't know how long we can hold out,\" the familiar voice emanates from Binaris' speakers, \"but I will await you in the Temple of Sapphire.\"";
$bronzeText[] = "\"Though I think this outpost might follow ye,\" Masaru adds, \"if ye can convince them that ye're really Chosen.\"";
$bronzeText[] = "What?! You run to the window-sized opening to peer out, wondering if there are two suns on this world, but it seems that the one is just making a return journey through the sky.";
$bronzeText[] = "Straining your newfound power to its limit, you make out that they are all wearing black armour and moving in a tight formation. At their current speed it won't be long before they reach the outpost.";
$bronzeText[] = "You would test the theory, but you're afraid there isn't time for that, so you take the bag from Krox; you never know what you might need.";
$bronzeText[] = "Binaris informs you of the outpost's defensive capabilities and you have her activate landmines around the perimeter.";
$bronzeText[] = "You ask her about the rest of the Roughlanders and learn that they will be evacuating to the nearest other outposts.";
$bronzeText[] = "Or is there? You wish desperately for your power to be of use and miraculously an obscuring fog begins to rise from the sand in response.";
$bronzeText[] = "You could swear you saw a dark blue fin break the surface of the sand a ways out from your group and you hope it's just a vision brought on by the heat.";
$bronzeText[] = "Reputation? You question Dahlia, but it's Masaru that answers you with a shudder, \"No one who's gone there has ever come back.\"";
$bronzeText[] = "Dahlia speaks sense and, without hesitating, you leap from the back of the mount and are thankful you did when the thing explodes moments later. ";
$bronzeText[] = "You ask around for more rope, but don't find any, so once again you turn to your power for help.";
$bronzeText[] = "You try that arrow thing again, but this time you manage to make one that glows; that's pretty useful!";
$bronzeText[] = "Thinking fast, you grab for the bag of weapons that you've hauled all this way from the outpost; you or Dahlia might have need of them.";
$bronzeText[] = "Her commanding voice snaps you back into action and you join Dahlia in putting some distance between yourself and the deadly creature.";
$bronzeText[] = "You rush forward, hoping that your power can be enough to save him, but it's clear right away that you're too late.";
$bronzeText[] = "\"One of them got me, but I'll be fine,\" Masaru tells you, handing you a small device. \"I just need a Flaqqer and I'll be as good as new in a bit.\"";
$bronzeText[] = "You shudder watching the vibrant pink creature writhe as it undulates through the jungle terrain and hope that it doesn't decide that humans are prey.";
$bronzeText[] = "You help the Roughlanders build a makeshift wooden barricade and, with the help of your powers, you set it on fire.";
$bronzeText[] = "You decide that you won't range too far out from the group that's carrying Masaru; you might be needed.";
$bronzeText[] = "You raise your arms and call on your power to protect the Roughlanders and yourself.";
$bronzeText[] = "Thinking fast, you throw it some food from your pack. Okay, now you've got it on your side, but you still need to convince it to attack the undead.";
$bronzeText[] = "There are the others! You're happy to see the survivors of last night's horror until you see Masaru's ashen face - is he...?";
$bronzeText[] = "Not sure what to make of this stranger, you introduce yourself and let her know how worried you are about Masaru.";
$bronzeText[] = "The city, long ago covered in snow, is now a desiccated ruin and no one lives here or is here to greet you.";
$bronzeText[] = "With the table spun all the way to the left you see constellations that you recognize from Earth... Is that a part of the solution?";
$bronzeText[] = "Bracing yourself with your power at the ready to defend you, you pull the red lever for Rubia, but that only succeeds in firing out a jet of flames.";
$bronzeText[] = "This place feels like a military base and you find dormitories with well-preserved standard issue clothes and, strangely, a few coins from Earth!";
$bronzeText[] = "You navigate through the menus and discover a map of the temple layout, indicating the main server.";
$bronzeText[] = "Relying on your power, you bravely touch the skeletal remains and receive a vision of the Croatin's final moments.";
$bronzeText[] = "The doors shut, locking you inside, and you close your eyes, trusting in your power to save you from whatever is going to happen in here.";
$bronzeText[] = "You're a little overwhelmed, but they seem friendly enough. Except for the heavily armoured Lady Adel; she's got a sour expression.";
$bronzeText[] = "You enter a room at Sir Sabien's instruction and are surprised when a program activates allowing you to speak to the spirit of Yuko Seig.";
$bronzeText[] = "The bat-woman, Ris, demonstrates each type of power, flying about the training room on her wings and playfully attacking Sir Sabien.";
$bronzeText[] = "Studying the map Dahlia gave you, you wonder how Taiyou in the northwest has managed to stay free of the Vile Emperor's control.";
$bronzeText[] = "Some outpost leaders are more responsive than others, but a few decide to join you.";
$bronzeText[] = "Fallen debris and a strange glowing, oozing substance on the walls; you fear a cave-in but the reality is much worse.";
$bronzeText[] = "\"Oho! The Vile Emperor?! Coming here?\" The outpost leader is a very large and obnoxious man. \"You're a Chosen looking for an army, are you?\"";
$bronzeText[] = "Looks like some people from this outpost are willing to join you after all; as you reach Adel she is talking to five tough-looking strangers";
$bronzeText[] = "You ask him if there's anything that might scare a sand crawler off. \"Only a bigger sand crawler,\" he replies.";
$bronzeText[] = "Taking the warning seriously, you step forward and take Masaru, Adel, Sabien and Lady Sirrah with you.";
$bronzeText[] = "You demand that your mysterious ally show himself and you are introduced to a towering vulture-like creature with ancient, knowing eyes and a friendly demeanor.";
$bronzeText[] = "Lord Hex's warning was right; there is a Deathsquad encampment guarding the way into Taiyou.";
$bronzeText[] = "You duck your head and cringe, pretending to be a scared Roughlander refugee in awe over the lake you can see beyond the soldiers.";
$bronzeText[] = "A Roughlander cloak is about the only thing that might cover up Aysel's amour, but it's a flimsy disguise; you hope no one questions it.";
$bronzeText[] = "\"Are there any outpost leaders among you?\" Lady Sirrah is allowed to keep her weapons with her due to her position.";
$bronzeText[] = "The people seem friendly enough and helpful, but their overly-cheerful nature strikes you as odd.";
$bronzeText[] = "\"Oh, you want Tathos. You'll like her, she's real smart.\" Everyone tells you the same thing, and eventually you're able to track the elusive Tathos down.";
$bronzeText[] = "You question the residents and discover that the longer a person has been here, the more trouble they seem to have remembering who they are or how they got here.";
$bronzeText[] = "You grab the glass out of Aysel's hand and move the plate of food away from her before she can reach for it.";
$bronzeText[] = "These people are nearly mindless, all you'll need to do is create a suitable distraction and take down the fence, and then you can escape in all the confusion.";
$bronzeText[] = "Almost there, but there's just one problem; Aysel struggles so hard in Adel's grasp that she breaks free and tries to return to the outpost.";
$bronzeText[] = "Sneaking back into the compound is easy, as the guards are trying to round up any stray Roughlanders after the fence went down.";
$bronzeText[] = "You can't take him with you just yet; he'd be too much trouble with the poison in his system, but maybe Masaru can help you.";
$bronzeText[] = "If you're going through the city, you're going to need disguises. Your Roughlander clothes will certainly attract the wrong kind of attention.";
$bronzeText[] = "Oh no! Aysel's stumbling and even Adel is having a hard time hurrying her sister along; is there a way to use your power to help?";
$bronzeText[] = "She has a temperature and her eyes are glazed; you wonder if she can see anything as she thrashes with uncontrolled spasms.";
$bronzeText[] = "As Adel busies herself making the fire, you settle down with your samples and use your power to examine them.";
$bronzeText[] = "In your mind's eye you see Masaru stumbling along the streets of Taiyou, the map you left him gripped tightly in one hand as his eyes glaze over like Aysel's.";
$bronzeText[] = "Closing your eyes and feeling outwards with your power, you find that you are able to sense Sir Sabien and can point right to him.";
$bronzeText[] = "You tell Sir Rama of Aysel's condition, hoping that perhaps someone in Taiyou has heard of it before and knows of an antidote.";
$bronzeText[] = "Each substance reacts a little differently with the contaminant, but none seems to reverse the spread of the green particles in the water.";
$bronzeText[] = "\"A strange request,\" Sir Rama comments, \"but I'll see what I can do, though it may take some time.\"";
$bronzeText[] = "Aysel seems to be responding to the salt-water treatment you've devised, but it looks like it going to be a long process.";
$bronzeText[] = "The first place to check is the storehouse you directed him too. He's not there, but you do find a Roughlander already dead from the poison.";
$bronzeText[] = "Drawing on your power, you realize that you can actually smell the blood on the page in your hands and that it is different from that of the Roughlander downstairs.";
$bronzeText[] = "You give the Roughlanders some of the salt-water from your canteen, but they're so far gone that you're not even sure it will help them.";
$bronzeText[] = "Tall, longhaired, with a katana and a long jacket, this man's power gleams a dull red in your mind's eye and you realize that you've seen him before.";
$bronzeText[] = "Knowing that you can't move Masaru in the state he's in without risking his life further, you realize that only your power can see you safely out of this.";
$bronzeText[] = "There being nothing you can do right now, you wander down to inspect the cause of all of this - the contaminated fountain in the outpost yard.";
$bronzeText[] = "\"Though if it's Masaru's life that concerns you, there is one thing that might save him,\" Sabien informs you.";
$bronzeText[] = "The sparring is hard to watch. Every time Masaru is injured Ris is there to heal him, but he's given little time to recover before Sir Sabien challenges him again.";
$bronzeText[] = "You've never dealt with royalty before; perhaps it would be best if you asked Sir Sabien for advice.";
$bronzeText[] = "No more hiding; the Regent, Lord Viron, knows you are here and it's time that everyone else did as well.";
$bronzeText[] = "Fuzen verbally attacks each of the Knights in turn, as you wait for the palace guards to admit you, until he finally turns his attention back to you.";
$bronzeText[] = "It seems that Lord Viron's methods follow some archaic ritual of diplomacy that you are unfamiliar with.";
$bronzeText[] = "You introduce yourself to the Prince as Lord Viron excuses himself from the room to attend to matters of state.";
$bronzeText[] = "\"Oh, I know who's responsible,\" Narlhep tells you. \"It was the Oujou.\"";
$bronzeText[] = "You tell the prince of your suspicions and they are confirmed when the sorceress' expression fills with rage at your accusation.";
$bronzeText[] = "Concentrating fiercely, you use your power to explode a hole in the floor to provide an escape route for you and the prince.";
$bronzeText[] = "\"I'll help you if I can,\" Narlhep offers. \"It's the least I can do after you saved my life.\"";
$bronzeText[] = "\"The Leyins are a magical defense system. They're invisible lines of power that criss-cross the country, allowing Viron to sense us if we cross them.\"";
$bronzeText[] = "Narlhep spends some time explaining Lord Viron's treachery and his current predicament to the young lord, but Sir Rama requires proof.";
$bronzeText[] = "The Temple doors are open, but Lord Viron is nowhere to be seen.";
return $bronzeText[$MissionNo];
}

function GetSilverText($MissionNo)
{
$silverText[] = "\"We're having a swim meet on Thursday!\" The school president announces, drawing your attention, and by the time you look back the angry stranger is gone.";
$silverText[] = "No one seems to be home there either... now you're getting kind of worried.";
$silverText[] = "The team's going out to celebrate and you're invited; karaoke anyone?";
$silverText[] = "It's dark, but the phone is still ringing, giving you something to follow - is the sound coming from the sandbox?";
$silverText[] = "\"Help is on the way,\" the operator informs you and you stand up ready to defend yourself until the police can arrive.";
$silverText[] = "Backing up, you wish for a way out of this and, to your surprise, a light begins to glow from your chest which causes the suit of armour to appear to be moving in slow motion.";
$silverText[] = "\"In Rubia's name - Die!\" The Vile Emperor threatens you with death; maybe you'd better accept this woman's proposal.";
$silverText[] = "Your alarm clock sounds again, waking you to another Tuesday... Okay, seriously?!";
$silverText[] = "The next Tuesday, you skip school and head to the hospital, hoping to use this time fluctuation to stop Shuzhue's kidnapping.";
$silverText[] = "\"Terminating the program,\" the strange voice announces before there is an explosion of light and sound that overloads your senses.";
$silverText[] = "There, ahead, a cloud of dust surrounds two people on horses that glint strangely in the sun. Hoping they're not a mirage, you wave your arms frantically to get their attention.";
$silverText[] = "Realizing that you've got nothing to bargain with, you admit as much and Corporal Dahlia laughs, breaking the tension. \"Aye, I can see as much.\"";
$silverText[] = "The metal is hot and the 'mount,' as Masaru calls it, is clearly not meant for two which leaves you bouncing about on the thing's rump. ";
$silverText[] = "\"Make trouble for the Roughlanders or this outpost and I will take your head myself,\" he states and his meaning is clear enough.";
$silverText[] = "The next is a picture of the angry blonde stranger. You protest and demand to know who he is, but the picture changes to show an unfamiliar room instead.";
$silverText[] = "\"The Vile Emperor is a Chosen of Rubia,\" she continues robotically, \"and has four Talons to serve him among his innumerable forces...\"";
$silverText[] = "There is a second message at a later date; \"The Vile Emperor is here, bent on destruction. I will leave...\" The message ends abruptly.";
$silverText[] = "You will your power to manifest and, though all you manage is a small splash of water, Masaru seems really impressed.";
$silverText[] = "At the risk of sounding crazy, you find a Roughlander to question about this phenomenon and learn that it never gets dark here; the sun always 'bounces' this way.";
$silverText[] = "Wait, didn't Binaris mention something about the Vile Emperor's forces? You're afraid this might be them; you'd better warn everyone.";
$silverText[] = "\"Do we fight or flee?\" Krox demands and you inform him diplomatically that you need to live to reach the Temple of Sapphire.";
$silverText[] = "In a flash of insight, you download Binaris' program and decide to take her with you to the Temple of Sapphire.";
$silverText[] = "\"Masaru's handling our supplies,\" Dahlia adds. \"Come on, let's get you a mount of yer own.\"";
$silverText[] = "You cheer internally as your mount races away from the abandoned outpost, but then cringe as Krox leaps forward to begin the battle and you remember the landmines you set.";
$silverText[] = "\"That's a sand shark,\" Masaru informs you. \"There's lots of dangerous creatures that live out in the sand, so don't go wandering off by yourself.\"";
$silverText[] = "You consider the Deathsquad and figure that the evil you don't know is better than the one you do, so you tell the Roughlanders to head for the mountains.";
$silverText[] = "\"They blow up when they run out of steam,\" Dahlia explains, \"and we don't have the water to spare for them.\"";
$silverText[] = "Raising your arms, you manifest an arrow that trails like rope and fire it close enough for Ticket to grab onto.";
$silverText[] = "Masaru takes one of your glowing arrows in hand and heads off to scout, \"Hey, thanks!\"";
$silverText[] = "The dark tunnel is filled with the sound of tearing rock as you reach Dahlia's side and note the terror in her expression; what's coming for you?";
$silverText[] = "\"My bolt thrower's not enough to slow it down!\" Dahlia yells and you hand her the bag of weapons you brought with you, realizing that there is something you can do to help.";
$silverText[] = "You can still hear the thing that's chasing you, so you keep running for the exit, but you can't help but wonder what happened to that Roughlander.";
$silverText[] = "You do as he instructs with the device as Masaru explains that a Flaqqer injects a person with sand crawler larvae in order to heal them faster.";
$silverText[] = "\"Hey look, supper!\" Ticket announces, landing in front of you, holding a small mammal in his hands. \"Let's head back.\"";
$silverText[] = "You can see that the undead creatures shy back from the light of the flames, but it's not going to be enough to completely deter them.";
$silverText[] = "As it continues to darken in the valley, you hear the chilling cry of the undead and you shudder with the knowledge of what's coming for you.";
$silverText[] = "It's working, but you can't keep this up for long. Wait - what's that in the trees?";
$silverText[] = "Relying on your power once more, you're not sure what you did, but suddenly the animal beneath you leaps into action; that's more like it!";
$silverText[] = "Masaru flinches in the sunlight, his face contorting just like one of the undead - oh no, he's becoming one of them!";
$silverText[] = "\"You're not human, are you?\" Lilyth asks. \"I'm not supposed to help humans, but if you're not, I can stop him from becoming one of my guardians.\"";
$silverText[] = "You discover a large door you think is the Temple itself, but there doesn't seem to be a way to open it.";
$silverText[] = "You've done it! Spinning out the patterns from Earth to what must be the stars for this planet seems to do the trick.";
$silverText[] = "With a stroke of genius, you remember that Sapphiros stands for balance and, convincing the Roughlanders to help you, you pull all the levers at once.";
$silverText[] = "At the end of one hall, a larger door reveals a room coated in ice and within it is cold enough that you need your power to even let you be in here for long.";
$silverText[] = "Without even thinking, you use your power to send out a message to the Roughlanders to show them where to meet you; you may need Binaris' help for this.";
$silverText[] = "The Vile Emperor did this when he attacked the temple and this is the room from where the Tuesdays program was running!";
$silverText[] = "You open your eyes to find five outlandish strangers standing before you; one of which is neither human nor Croatin.";
$silverText[] = "\"Hey, how's it goin'?\" Jeth says by way of greeting as Ris, the strange half-woman, half-bat creature, regards you curiously";
$silverText[] = "\"The only way to go home is to use the Splitters - they are powerful devices that make travel between worlds possible.\"";
$silverText[] = "\"Remember fear; it can fuel your power,\" Sabien instructs you and, with his help, you consciously use your powers to defend yourself.";
$silverText[] = "Still, it's the safest place you can see and maybe you can learn something from how they've kept him at bay.";
$silverText[] = "One outpost Leader, Lady Sirrah, pledges her whole outpost to follow you wherever you'll lead them.";
$silverText[] = "Radiation! The glowing substance is radioactive; you'd better get everyone out of here quick before they start to feel the effects.";
$silverText[] = "\"I'll tell my people you stopped by, but don't expect any of them to join you. We're quite safe here, you know. The Deathsquads protect us from people like you.\"";
$silverText[] = "\"I'm Razor and this is my gang,\" the leader informs Adel. \"We'd like to join you.\"";
$silverText[] = "Thinking fast, you organize your caravan of Roughlanders to move in a wave pattern that should fool any hungry sand crawlers into thinking you're too big to be prey.";
$silverText[] = "The conversation is tense and they don't trust you, but you manage to convince them to escort you to the leader of this outpost.";
$silverText[] = "\"And I am not the only one,\" he assures you, \"there are other allies.\"";
$silverText[] = "You'll have to be extra cunning to get past them and into Taiyou safely; the camp looks like a permanent settlement.";
$silverText[] = "Razor did mention that Croatins weren't allowed in Taiyou, but there's one ahead flimsily disguised by a cloak; what is he thinking walking up to the Deathsquad soldiers?";
$silverText[] = "That Croatin from earlier is about to be caught until Adel appears from nowhere to challenge the Deathsquad soldiers single-handedly; so much for keeping a low profile!";
$silverText[] = "\"Should I name you one of my guards, so you can keep your weapons?\" Sirrah asks, but you tell her that she should name Adel and Masaru, as they'd never give their weapons up willingly.";
$silverText[] = "The fence surrounds the compound and is locked and guarded, but none of the residents seem to mind; what exactly is going on here?";
$silverText[] = "\"Hi, I'm Tathos.\" You introduce yourself and explain your request, but Tathos seems as dumb as a brick.";
$silverText[] = "You meet back up with Adel and Aysel in the outpost building, only to discover that the strangeness of this place is beginning to affect them too!";
$silverText[] = "Adel grabs her sister by the arm and hauls her to her feet before turning to you. \"Now, how do you propose we get out of here?\"";
$silverText[] = "You let out a deafening roar and then send out four arrows in different directions to explode the fence around the outpost.";
$silverText[] = "Thinking fast you call upon your power to turn the three of you to mist and drift toward a storehouse window.";
$silverText[] = "On the second floor of the outpost, you take some possibly contaminated water and pour it into your canteen; maybe an antidote can somehow be found?";
$silverText[] = "Writing hasty instructions, you press a map to where you're staying in his hands and tell him to take as many as will follow him and meet you there, before using your power to fly you out the window.";
$silverText[] = "You find a discarded set of Taiyoun robes for yourself and it's a simple matter to fashion cloaks from two old blankets found in storehouse crates to cover Aysel and Adel.";
$silverText[] = "Thanks to your power, Aysel begins to hover a few inches above the ground and to make sure no one pays attention to you. You also use your power to ensure that passersby will look away again, confused.";
$silverText[] = "There has to be something you can do to calm her down, maybe your power can help?";
$silverText[] = "The cheese doesn't give you much, but under the scrutiny of your power, the water appears to be filled with green particles.";
$silverText[] = "Masaru falls limply to the cobblestones, and you realize that if Aysel's condition is any indication, you've left him and the others behind to die.";
$silverText[] = "He's north, but not far, he must have taken the long way around and now that you know where he is, you can catch up to him.";
$silverText[] = "\"I'll have my medic look her over,\" Sir Rama offers, \"and in the mean time, I can at least give you some supplies.\"";
$silverText[] = "\"Hey, whatcha doin'?\" Jeth asks. \"Seems like it spreads in water; have you tried breaking it down with salt?\"";
$silverText[] = "After Sir Rama departs you get an idea; sweat! Using your power to help, you make everyone run around to make the salt you need to help Aysel.";
$silverText[] = "Loading the wagon Sir Rama provided, you're ready to head back south, but Aysel won't fit in the wagon. It looks like you'll have to use your power to bring her along safely.";
$silverText[] = "Worried, you check the rest of the building and find a note stuck to the wall with one of Masaru's knives.";
$silverText[] = "You quickly head outside, realizing that if you can follow the scent, it might lead you to Masaru.";
$silverText[] = "Fear growing, you close your eyes and use your power to sense for Masaru; he has to be here somewhere.";
$silverText[] = "This is one of Shuzhue's kidnappers; one of the Talons of the Vile Emperor. It seems that he's tracked you here the same way you did to Masaru!";
$silverText[] = "As Fuzen leaps, you cause Masaru to disappear with your power and then hop down from the crates, increasing your speed until you are almost invisible.";
$silverText[] = "Searching for answers, your power activates and you receive a vision of a time when this fountain was cursed by the powerful magic of the \"Oujou\", wielded by a member of the royal line.";
$silverText[] = "Following Sabien's advice, you perform the ancient ritual to share some of your power as a Chosen, making Masaru one of the Knights of Sapphiros.";
$silverText[] = "\"Remember fear, Masaru,\" Sabien tells him once he's finally managed to gain the upper hand. \"Death is a worthy adversary, but also a necessary one.\"";
$silverText[] = "\"It is only prudent that you gather more information about affairs in Taiyou and this Lord Regent in particular,\" Sabien advises. \"Blend in and ask around.\"";
$silverText[] = "With the Knights behind you, you march proudly through the streets of Taiyou to the expressions of mingled fear and awe on the faces of the city's citizens.";
$silverText[] = "You realize that all Fuzen is trying to do is rile you, so you stand tall and refuse to respond to his taunts.";
$silverText[] = "Despite the word games, in the end it seems that you've convinced the regent to allow you to meet the Prince.";
$silverText[] = "You hear the sound of the double doors shutting behind you before you sense the magic that has just sealed the room.";
$silverText[] = "As if summoned, a sorceress matching the description that Sabien gave you earlier appears in the throne room to stand behind the prince.";
$silverText[] = "\"The Laws of Kingship.\" A document on display in the room catches your eye and before you've really thought it through, you use your power to cause it to disappear.";
$silverText[] = "As the Oujou launches her second assault, you and Narlhep leap through the hole in the floor and make off through the palace to safety.";
$silverText[] = "Following Sabien's instructions, Narlhep lets some of his blood trickle into the fountain, offering a sacrifice to end the curse caused by his family line.";
$silverText[] = "Marching northwards toward the Temple, you come across the first Leyin, but with your powers it's a simple matter to see the line of power and cross it undetected.";
$silverText[] = "You offer to use your power to show him the memory of the Oujou's attack in the throne room and it seems to be enough to convince him.";
$silverText[] = "You call him out, declaring him traitor, only to be confronted by the evil Oujou, holding the crown of Taiyou in her hands.";
return $silverText[$MissionNo];
}

function GetMissionExp($MissionNo)
{
			$expWon[] = 1;

			$expWon[] = 2;

			$expWon[] = 2;

			$expWon[] = 3;

			$expWon[] = 4;

			$expWon[] = 6;

			$expWon[] = 6;

			$expWon[] = 7;

			$expWon[] = 8;

			$expWon[] = 10;

			$expWon[] = 12;

			$expWon[] = 14;

			$expWon[] = 15;

			$expWon[] = 17;

			$expWon[] = 18;

			$expWon[] = 18;

			$expWon[] = 20;

			$expWon[] = 19;

			$expWon[] = 23;

			$expWon[] = 25;

			$expWon[] = 27;

			$expWon[] = 30;

			$expWon[] = 29;

			$expWon[] = 32;

			$expWon[] = 33;

			$expWon[] = 32;

			$expWon[] = 35;

			$expWon[] = 36;

			$expWon[] = 35;

			$expWon[] = 40;

			$expWon[] = 41;

			$expWon[] = 41;

			$expWon[] = 42;

			$expWon[] = 44;

			$expWon[] = 43;

			$expWon[] = 48;

			$expWon[] = 50;

			$expWon[] = 52;

			$expWon[] = 50;

			$expWon[] = 56;

			$expWon[] = 60;

			$expWon[] = 64;

			$expWon[] = 69;

			$expWon[] = 72;

			$expWon[] = 77;

			$expWon[] = 81;

			$expWon[] = 86;

			$expWon[] = 90;

			$expWon[] = 94;

			$expWon[] = 100;

			$expWon[] = 57;

			$expWon[] = 60;

			$expWon[] = 63;

			$expWon[] = 64;

			$expWon[] = 67;

			$expWon[] = 71;

			$expWon[] = 74;

			$expWon[] = 77;

			$expWon[] = 81;

			$expWon[] = 85;

			$expWon[] = 112;

			$expWon[] = 116;

			$expWon[] = 118;

			$expWon[] = 124;

			$expWon[] = 130;

			$expWon[] = 136;

			$expWon[] = 144;

			$expWon[] = 151;

			$expWon[] = 160;

			$expWon[] = 166;

			$expWon[] = 175;

			$expWon[] = 181;

			$expWon[] = 188;

			$expWon[] = 195;

			$expWon[] = 198;

			$expWon[] = 207;

			$expWon[] = 217;

			$expWon[] = 226;

			$expWon[] = 234;

			$expWon[] = 247;

			$expWon[] = 257;

			$expWon[] = 261;

			$expWon[] = 271;

			$expWon[] = 280;

			$expWon[] = 291;

			$expWon[] = 308;

			$expWon[] = 317;

			$expWon[] = 327;

			$expWon[] = 334;

			$expWon[] = 340;

			$expWon[] = 360;

			$expWon[] = 381;

			$expWon[] = 396;

			$expWon[] = 407;

			$expWon[] = 418;

			$expWon[] = 431;

			$expWon[] = 447;

			$expWon[] = 464;

			$expWon[] = 486;

			$expWon[] = 506;
			return $expWon[$MissionNo];
}

function GetMissionKrevels($MissionNo)
{
			$krevelsWon[] = 3;

			$krevelsWon[] = 7;

			$krevelsWon[] = 10;

			$krevelsWon[] = 16;

			$krevelsWon[] = 23;

			$krevelsWon[] = 30;

			$krevelsWon[] = 36;

			$krevelsWon[] = 44;

			$krevelsWon[] = 55;

			$krevelsWon[] = 67;

			$krevelsWon[] = 90;

			$krevelsWon[] = 100;

			$krevelsWon[] = 130;

			$krevelsWon[] = 170;

			$krevelsWon[] = 160;

			$krevelsWon[] = 180;

			$krevelsWon[] = 210;

			$krevelsWon[] = 240;

			$krevelsWon[] = 260;

			$krevelsWon[] = 290;

			$krevelsWon[] = 500;

			$krevelsWon[] = 570;

			$krevelsWon[] = 600;

			$krevelsWon[] = 650;

			$krevelsWon[] = 680;

			$krevelsWon[] = 720;

			$krevelsWon[] = 750;

			$krevelsWon[] = 780;

			$krevelsWon[] = 820;

			$krevelsWon[] = 950;

			$krevelsWon[] = 1100;

			$krevelsWon[] = 1200;

			$krevelsWon[] = 1320;

			$krevelsWon[] = 1400;

			$krevelsWon[] = 1450;

			$krevelsWon[] = 1530;

			$krevelsWon[] = 1800;

			$krevelsWon[] = 1900;

			$krevelsWon[] = 1980;

			$krevelsWon[] = 2200;

			$krevelsWon[] = 3000;

			$krevelsWon[] = 3400;

			$krevelsWon[] = 3800;

			$krevelsWon[] = 4000;

			$krevelsWon[] = 4300;

			$krevelsWon[] = 4600;

			$krevelsWon[] = 5100;

			$krevelsWon[] = 5500;

			$krevelsWon[] = 5800;

			$krevelsWon[] = 6100;

			$krevelsWon[] = 6950;

			$krevelsWon[] = 7250;

			$krevelsWon[] = 7500;

			$krevelsWon[] = 7700;

			$krevelsWon[] = 7900;

			$krevelsWon[] = 8100;

			$krevelsWon[] = 8400;

			$krevelsWon[] = 9000;

			$krevelsWon[] = 9600;

			$krevelsWon[] = 10400;

			$krevelsWon[] = 11000;

			$krevelsWon[] = 11500;

			$krevelsWon[] = 13000;

			$krevelsWon[] = 13100;

			$krevelsWon[] = 13900;

			$krevelsWon[] = 14000;

			$krevelsWon[] = 15000;

			$krevelsWon[] = 15700;

			$krevelsWon[] = 16500;

			$krevelsWon[] = 17400;

			$krevelsWon[] = 19500;

			$krevelsWon[] = 21700;

			$krevelsWon[] = 22000;

			$krevelsWon[] = 23500;

			$krevelsWon[] = 24700;

			$krevelsWon[] = 25600;

			$krevelsWon[] = 26900;

			$krevelsWon[] = 28400;

			$krevelsWon[] = 29300;

			$krevelsWon[] = 31000;

			$krevelsWon[] = 31200;

			$krevelsWon[] = 32300;

			$krevelsWon[] = 34800;

			$krevelsWon[] = 36100;

			$krevelsWon[] = 37500;

			$krevelsWon[] = 38000;

			$krevelsWon[] = 39800;

			$krevelsWon[] = 41200;

			$krevelsWon[] = 43600;

			$krevelsWon[] = 45000;

			$krevelsWon[] = 45000;

			$krevelsWon[] = 46500;

			$krevelsWon[] = 48100;

			$krevelsWon[] = 49300;

			$krevelsWon[] = 52200;

			$krevelsWon[] = 55100;

			$krevelsWon[] = 57800;

			$krevelsWon[] = 60900;

			$krevelsWon[] = 62500;

			$krevelsWon[] = 65000;
			return $krevelsWon[$MissionNo];
}

//a function to feed the area data for missions
function MissionData($MissionGroup, $uid)
{
	$db_server = mysql_connect(DB_HOST, DB_USER, DB_PW);
	
	if(!$db_server) die("Unable to connect to MySQL: " . mysql_error());
	
	mysql_select_db(DB_NAME) or die("Unable to select database: " . mysql_error());
	
	
	if(!$result) die("Database access failed: " . mysql_error());
	switch($MissionGroup)
	{
		case 0:
			$query = "SELECT jExp1, jExp2, jExp3, jExp4, jExp5, jExp6, jExp7, jExp8, jExp9, jExp10, jTier1 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 1:
			$query = "SELECT jExp11, jExp12, jExp13, jExp14, jExp15, jExp16, jExp17, jExp18, jExp19, jExp20, jTier2 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 2:
			$query = "SELECT jExp21, jExp22, jExp23, jExp24, jExp25, jExp26, jExp27, jExp28, jExp29, jExp30, jTier3 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 3:
			$query = "SELECT jExp31, jExp32, jExp33, jExp34, jExp35, jExp36, jExp37, jExp38, jExp39, jExp40, jTier4 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 4:
			$query = "SELECT jExp41, jExp42, jExp43, jExp44, jExp45, jExp46, jExp47, jExp48, jExp49, jExp50, jTier5 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 5:
			$query = "SELECT jExp51, jExp52, jExp53, jExp54, jExp55, jExp56, jExp57, jExp58, jExp59, jExp60, jTier6 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 6:
			$query = "SELECT jExp61, jExp62, jExp63, jExp64, jExp65, jExp66, jExp67, jExp68, jExp69, jExp70, jTier7 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 7:
			$query = "SELECT jExp71, jExp72, jExp73, jExp74, jExp75, jExp76, jExp77, jExp78, jExp79, jExp80, jTier8 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 8:
			$query = "SELECT jExp81, jExp82, jExp83, jExp84, jExp85, jExp86, jExp87, jExp88, jExp89, jExp90, jTier9 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		case 9:
			$query = "SELECT jExp91, jExp92, jExp93, jExp94, jExp95, jExp96, jExp97, jExp98, jExp99, jExp100, jTier1 FROM CW_Users WHERE id='" . $uid . "'";
			$result = mysql_query($query);

			if(!$result) die("Database access failed: " . mysql_error());
			$rows = mysql_fetch_row($result);
			$jExp[] = $rows[0];
			$jExp[] = $rows[1];
			$jExp[] = $rows[2];
			$jExp[] = $rows[3];
			$jExp[] = $rows[4];
			$jExp[] = $rows[5];
			$jExp[] = $rows[6];
			$jExp[] = $rows[7];
			$jExp[] = $rows[8];
			$jExp[] = $rows[9];
			$jTier = $rows[10];
			break;
		default:
			mysql_close($db_server);
			return;
			break;
	}
	mysql_close($db_server);
	
	for($i = 0; $i < 10; $i++)
	{
		$missionNumber = $i + 10 * $MissionGroup;
		echo "<div class='missionbox' style='top:" . (283 * ($i % 10)) . "px; color:black;'>";
		//TODO
		//CHANGE THIS FROM A POST ACTION TO AN ONCLICK JS ACTION
		echo <<<_END
		
		<form method="post" action="missions.php">
_END;
		echo "<div style='position:absolute; left:9px; top:5px;'>" . GetMissionTitle($missionNumber) . $groupText . "</div>";
		//if($i == $job && $Mount && $didMission != 0)
		//	$krevelsWon[$i] /= 2;
		echo "<div style='position:absolute; left:472px; top:5px;'>Krevels Earned: " . GetMissionKrevels($missionNumber) . "</div>";
		//if($i == $job && $ExtraBelts && $didMission != 0)
		//	$expWon[$i] /= 2;
		echo "<div style='position:absolute; left:618px;  top: 5px; width:120px; text-align:right;'>Experience: " . GetMissionExp($missionNumber) . "</div><br/><br/>";
		if($jTier > 1 || $jExp[$i] == 100)
		{
		echo "<img style='position:absolute; top:112px; left:15px;' src='assets/Star_Yellow.png'/>";
		echo "<div style='position:absolute; top:112px; left:35px; width:695px;'>" . GetBronzeText($missionNumber) . "</div>";
		}
		else
		echo "<img style='position:absolute; top:112px; left:15px;' src='assets/Star_White.png'/>";
		if($jTier > 2 || ($jTier == 2 && $jExp[$i] == 100))
		{
		echo "<img style='position:absolute; top:147px; left:15px;' src='assets/Star_Yellow.png'/>";
		echo "<div style='position:absolute; top:147px; left:35px; width:695px;'>" . GetSilverText($missionNumber) . "</div>";
		}
		else if($jTier == 2)
		echo "<img style='position:absolute; top:147px; left:15px;' src='assets/Star_White.png'/>";
		echo <<<_END
		<input type="hidden" name="posted" value="yes"/>
		<input type="hidden" name="job" value="$i"/>
		<input type="hidden" name="token" value="$access_token"/>
		<div style="position:absolute; top:36px; left:10px; width:720px; height:80px;"> GetMissionText($missionNumber) $groupText</div>
		<img src='assets/ExpBarBG.jpg' style='position:absolute; left:12px; top:242px;'/>
		<div style="position:absolute; left:12px; top:242px; width:111px;"><img src='assets/ExpBarFill.jpg' width='$jExp[$i]%' height='18px'/></div>
		<div style="position:absolute; top:245px; left:52px; color:white;">$jExp[$i]%</div>
		<div style="position:absolute; top:204px; left:12px;">
_END;
		if($missionNumber == 4)
		{
		echo " Chance for: <br/>Obscure Vision.";
		}
		else if($missionNumber == 16)
		{
		echo " Chance for: <br/>Snow.";
		}
		else if($missionNumber == 18)
		{
		echo " Chance for: <br/>Light Arrow.";
		}
		else if($missionNumber == 20)
		{
		echo " Chance for: <br/>Vanish Weapon.";
		}
		else if($missionNumber == 27)
		{
		echo " Chance for: <br/>Net Arrow.";
		}
		else if($missionNumber == 30)
		{
		echo " Chance for: <br/>Seeker Arrow.";
		}
		else if($missionNumber == 33)
		{
		echo " Chance for: <br/>Mist Travel.";
		}
		else if($missionNumber == 49)
		{
		echo " Chance for: <br/>Energy Draining Net.";
		}
		echo <<<_END
		</div>
		
_END;
		
		if($missionNumber == 5)
		{
		if($slowPerson < 1)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px; vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Slow Person' src='assets/power15.jpg'/><br/>X1</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Slow Person' src='assets/power15.jpg'/><br/>X1</div></div>";
		}
		else if($missionNumber == 6)
		{
		if($mistOut < 1)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Mist Out' src='assets/power21.jpg'/><br/>X1</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Mist Out' src='assets/power21.jpg'/><br/>X1</div></div>";
		}
		else if($missionNumber == 17)
		{
		if($createWater < 1)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Create Water' src='assets/power54.jpg'/><br/>X1</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Create Water' src='assets/power54.jpg'/><br/>X1</div></div>";
		}
		else if($missionNumber == 18)
		{
		if($arrow < 1)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Arrow' src='assets/power1.jpg'/><br/>1X</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Arrow' src='assets/power1.jpg'/>X1</div></div>";
		}
		else if($missionNumber == 19)
		{
		if($farSight < 1)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Far Sight' src='assets/power47.jpg'/><br/>X1</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Far Sight' src='assets/power47.jpg'/><br/>X1</div></div>";
		}
		else if($missionNumber == 23)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left; top:188px; left:127px;vertical-align:text-top;'>Requires:  <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Obscure Vision' src='assets/power37.jpg'/><br/>X5</div></div>";
		else if($missionNumber == 26)
		{
		if($gustOfWind < 10)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Gust of Wind' src='assets/power27.jpg'/><br/>X10</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Gust of Wind' src='assets/power27.jpg'/><br/>X10</div></div>";
		}
		else if($missionNumber == 27)
		{
		if($ropeArrow < 10)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Rope Arrow' src='assets/power41.jpg'/><br/>X10</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Rope Arrow' src='assets/power41.jpg'/><br/>X10</div></div>";
		}
		else if($missionNumber == 28)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Light Arrow' src='assets/power43.jpg'/><br/>X5</div></div>";
		else if($missionNumber == 31)
		{
		if($heal < 10)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>  Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Heal' src='assets/power23.jpg'/><br/>X10</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Heal' src='assets/power23.jpg'/><br/>X10</div></div>";
		}
		else if($missionNumber == 32)
		{
		if($nearSight < 10)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Near Sight' src='assets/power49.jpg'/><br/>X10</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Near Sight' src='assets/power49.jpg'/><br/>X10</div></div>";
		}
		else if($missionNumber == 33)
		{
		if($createWater < 20)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Create Water' src='assets/power54.jpg'/><br/>X20</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Create Water' src='assets/power54.jpg'/><br/>X20</div></div>";
		}
		else if($missionNumber == 34)
		{
		if($fireball < 10)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'> <a class='faction' href='store.php?token=$access_token'><img  title='Fireball' src='assets/power12.jpg'/><br/>X10</a></div> <div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Net Arrow' src='assets/power42.jpg'/><br/>X10</div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Fireball' src='assets/power12.jpg'/><br/>X10</div><div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Net Arrow' src='assets/power42.jpg'/><br/>X10</div></div>";
		}
		else if($missionNumber == 36)
		{
		if($penetrationArrow < 15)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Penetration Arrow' src='assets/power2.jpg'/><br/>X15</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Penetration Arrow' src='assets/power2.jpg'/><br/>X15</div></div>";
		}
		else if($missionNumber == 37)
		{
		if($intimidate < 15)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Intimidate' src='assets/power33.jpg'/><br/>X15</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Intimidate' src='assets/power33.jpg'/><br/>X15</div></div>";
		}
		else if($missionNumber == 39)
		{
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Snow' src='assets/power50.jpg'/><br/>X10</div></div>";
		}
		else if($missionNumber == 42)
		{
		if($resistHeat < 5)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Resist Heat' src='assets/power39.jpg'/><br/>X5</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Resist Heat' src='assets/power39.jpg'/><br/>X5</div></div>";
		}
		else if($missionNumber == 43)
		{
		if($resistCold < 5)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Resist Cold' src='assets/power40.jpg'/><br/>X5</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Resist Cold' src='assets/power40.jpg'/><br/>X5</div></div>";
		}
		else if($missionNumber == 44)
		{
		if($messageArrow < 20)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Message Arrow' src='assets/power44.jpg'/><br/>X20</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Message Arrow' src='assets/power44.jpg'/><br/>X20</div></div>";
		}
		else if($missionNumber == 45)
		{
		if($readMemoryPerson < 8)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Read Memory Person' src='assets/power60.jpg'/><br/>X8</a></div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><img  title='Read Memory Person' src='assets/power60.jpg'/><br/>X8</div></div>";
		}
		else if($missionNumber == 49)
		{
		if($largeShield < 20)
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'> Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'><a class='faction' href='store.php?token=$access_token'><img  title='Mist Travel' src='assets/power46.jpg'/><br/>X10</a></div> <div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Large Shield' src='assets/power30.jpg'/><br/>X20</div></div>";
		else
		echo "<div style='position:absolute; height:64px; width:128px; text-align:left;top:188px; left:127px;vertical-align:text-top;'>Requires: <div style='position:absolute; top:0px; left:75px; text-align:center;'> <img  title='Mist Travel' src='assets/power46.jpg'/><br/>X10</div><div style='position:absolute; top:0px; left:147px; text-align:center;'><img  title='Large Shield' src='assets/power30.jpg'/><br/>X20</div></div>";
		}
		/*
		if($didMission != 0 && $job == $i)
		{
		echo "<div style='position:absolute; top: 188px; left:346px;width:160px; height:84px;'>";
		if($didMission > 0)
		{
		if($Mount)
			$krevelsWon[$job] *= 2;
		if($ExtraBelts)
			$expWon[$job] *= 2;
		echo <<<_END
		<div style='width:94px;'>
		You earned:<br/>
		$expWon[$job] exp<br/>
		$krevelsWon[$job] krevels
		</div>
_END;
		if($drop == 1)
			echo $dropMsg;
		echo <<<_END
		<div style='position:absolute; bottom:0px;'>
		MISSION COMPLETE
		</div>
_END;
		}
		else
		{
		echo "<div style='position:absolute; top:10px; left:15px;'>" . $errorMsg . "</div>";
		}
		echo "</div>";
		}*/
		/*
		if($job == $i && $levelUp && $didMission != 0)
		echo  "<div style='position:absolute; top:190px; left:520px;'><img src='assets/Icon_LevelUp.png' width='64px' height='64px'/></div>";*/
		echo <<<_END
		
		<div style="position:absolute; left:595px; top:188px; width:200px; text-align:center;"> 
_END;
		/*
		if($i == $job && $didMission != 0)
			echo "$usedEnergy[$i] Energy<br/><input type='image' value='GO' src='assets/BTN_Again.png'/>";
		else
			echo "$usedEnergy[$i] Energy<br/><input type='image' value='GO' src='assets/BTN_Go.png'/>";
			*/
		echo <<<_END
		</div>
_END;
		/*
		if($i == 17 && !($i == $job && $levelUp == 1 && $didMission != 0))
		echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>SAPPHIROS</div>";
		else if ($i == 20 && !($i == $job && $levelUp == 1 && $didMission != 0))
		echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>MACHALITE</div>";
		else if($i == 36 && !($i == $job && $levelUp == 1 && $didMission != 0))
		echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>DAMOS</div>";
		else if($i == 37 && !($i == $job && $levelUp == 1 && $didMission != 0))
		echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>JEDEITE</div>";
		else if($i == 42 && !($i == $job && $levelUp == 1 && $didMission != 0))
		echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>SAPPHIROS</div>";
		else if($i == 45 && !($i == $job && $levelUp == 1 && $didMission != 0))
		echo "<div style='position:absolute; top:190px; left:520px;'>MUST BE <br/>CHOSEN OF <br/>RUBIA</div>";
		*/
		echo <<<_END
		
		</form>
		</div>
		<div style='border:0px; padding:0px; margin:0px; width:0px; height:8px; position:relative;'></div>
_END;
	
	}
}
?>
