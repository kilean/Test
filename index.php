﻿<?php
	header("Content-Type:text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Test</title>
    <style>
        /*CSS*/
        #conPanel {
            width: 60%;
            border: 10px solid #5c5c5c;
            box-shadow: 8px 8px 8px black;
            text-align: center;
            margin: auto;
            border-radius: 10px;
        }

            #conPanel > span {
                font-family: Webdings;
                font-size: 24pt;
                cursor: pointer;
                box-shadow: 3px 3px 2px black;
                border: 2px groove;
            }

        #vol {
            width: 300px;
        }

        #progress {
            background-color: #4800ff;
            height: 5px;
            width: 0;
        }

        #settime {
            background-color: lightgray;
            height: 5px;
            cursor: pointer;
        }

        #bottom {
            background-color: black;
        }

        #info {
            color: yellow;
        }

        #duration, #info2 {
            color: #00ff21;
        }

        #music {
            width: 100%;
            background-color: black;
            color: #00ff21;
            font-size: 14pt;
        }

        #volume {
            color: yellow;
            background-color: darkslategrey;
        }

        .line {
            width: 100%;
        }
    </style>
</head>
<body>
    <!--HTML-->
    <audio id="audio">
        <source id="s" src="Data/Data000.m" type="audio/mpeg" title="Little Hoot Theme Song" />
    </audio>
    <div id="conPanel">
        <select id="music">
            <option value="Data/Data000.m">Another realm</option>
            <option value="Data/Data001.m">Butterfly</option>
            <option value="Data/Data002.m">Fairy Wings</option>
            <option value="Data/Data003.m">Paper Clouds</option>
            <option value="Data/Data004.m">Straw Hats</option>
            <option value="Data/Data005.m">Sundial Dreams</option>
            <option value="Data/Data006.m">The Enchanted Garden</option>
            <option value="Data/Data007.m">The Purple Butterfly</option>
			<option value="Data/Data008.m">Through The Arbor</option>
			<option value="Data/Data009.m">Water Lilies</option>
        </select>

        <br /><br />
        <span id="play">4</span>
        <span id="stop"><</span>
        <span id="prevsong">9</span>
        <span id="nextsong">:</span>
        <span id="muted">V</span>
        <span id="loop">q</span>
        <span id="allloop">`</span>
        <span id="random">s</span>
        <br /><br />
        <div id="settime">
            <div id="progress"></div>
        </div>

        <div id="volume">
            <label for="vol">Volume：</label>
            <input id="vol" type="range" max="100" min="0" value="50" />
        </div>

        <div id="bottom">
            <div id="duration">
                00:00/00:00
            </div>
            <span id="info2"></span>
            <marquee id="info" width="100%">請按播放鍵~!!</marquee>
        </div>
    </div>
    <script>
        //JavaScript
        var play = document.getElementById("play");
        var stop = document.getElementById("stop");
        var vol = document.getElementById("vol");
        var audio = document.getElementById("audio");
        var s = document.getElementById("s");
        var info = document.getElementById("info");
        var music = document.getElementById("music");
        var prevsong = document.getElementById("prevsong");
        var nextsong = document.getElementById("nextsong");
        var muted = document.getElementById("muted");
        var duration = document.getElementById("duration");
        var progress = document.getElementById("progress");
        var settime = document.getElementById("settime");
        var loop = document.getElementById("loop");
        var info2 = document.getElementById("info2");
        var random = document.getElementById("random");
        var allloop = document.getElementById("allloop");

        audio.volume = vol.value / 100;

        play.addEventListener("click", PlaySong);
        vol.addEventListener('change', VolumeChange);
        stop.addEventListener('click', StopSong);
        music.addEventListener('change', SongSelect);
        prevsong.addEventListener('click', function () { SongChange(false) });
        nextsong.addEventListener('click', function () { SongChange(true) });
        muted.addEventListener('click', SetMuted);
        audio.addEventListener("play", getDuration);
        loop.addEventListener("click", loopSong);
        random.addEventListener("click", randomSong);
        allloop.addEventListener("click", allloopSong);

        //全曲循環
        function allloopSong() {
            if (info2.innerText != "全曲循環") {
                audio.loop = false;
                info2.innerText = "全曲循環";
            }
            else {

                info2.innerText = "";
            }
        }

        //隨機播放
        function randomSong() {
            if (info2.innerText != "隨機播放") {
                audio.loop = false;
                info2.innerText = "隨機播放"
            }
            else {
                info2.innerText = ""
            }
        }

        //單曲循環播放
        function loopSong() {
            if (info2.innerText != "單曲循環") {
                audio.loop = true;
                info2.innerText = "單曲循環"
            }
            else {
                audio.loop = false;
                info2.innerText = ""
            }
        }

        //用進度bar跳至指定時間
        settime.addEventListener("click", function (evnt) {
            var a = evnt.offsetX / 400;
            audio.currentTime = audio.duration * a;
        });

        //取得播放時間進度
        function getDuration() {
            durationTime = formatSecond(audio.duration);
            currentTime = formatSecond(audio.currentTime);
            duration.innerText = currentTime + "/" + durationTime;
            progress.style.width = Math.floor(audio.currentTime / audio.duration * 100) + "%";
            if (audio.currentTime <= audio.duration)
                setTimeout("getDuration()", "1000");
            if (audio.duration == audio.currentTime) {
                if (info2.innerText == "隨機播放") {
                    var r = Math.floor(Math.random() * music.options.length)
                    s.src = music.options[r].value;
                    s.title = music.options[r].text;
                    music.options[r].selected = true;
                    audio.load();
                    btnDicision();
                }
                else {
                    if (music.selectedIndex == music.options.length - 1) {
                        if (info2.innerText == "全曲循環") {
                            music.selectedIndex = 0;
                            btnDicision();
                        }
                        else
                            StopSong();
                    }
                    else {
                        SongChange(true);
                    }
                }
            }
        }

        //將秒轉成時分秒
        function formatSecond(secs) {
            var h = Math.floor(secs / 3600);
            var min = Math.floor((secs - (h * 3600)) / 60);
            var sec = parseInt(secs - (h * 3600) - (min * 60));
            min = (min < 10) ? "0" + min : min;
            sec = (sec < 10) ? "0" + sec : sec;
            return min + ":" + sec;
        }

        //靜音功能
        function SetMuted() {
            if (muted.innerText == "V") {
                audio.muted = true;
                muted.innerText = "U";
            }
            else {
                audio.muted = false;
                muted.innerText = "V";
            }
        }

        //上一曲下一曲
        function SongChange(boolNextPrev) {
            if (boolNextPrev) {
                index = music.selectedIndex + 1;
            }
            else {
                index = music.selectedIndex - 1;
            }
            s.src = music.options[index].value;
            s.title = music.options[index].text;
            music.options[index].selected = true;
            audio.load();
            btnDicision();
        }

        //音樂選擇
        function SongSelect() {
            s.src = music.options[music.selectedIndex].value;
            s.title = music.options[music.selectedIndex].text;
            audio.load();
            btnDicision();
        }

        //判斷換歌曲時是否直接播放
        function btnDicision() {
            if (play.innerText == ";") {
                play.innerText = "4";
                PlaySong();
            }
        }

        //音樂播放
        function PlaySong() {
            if (play.innerText == "4") {
                audio.play();
                info.innerText = "現正播放:" + s.title;
                play.innerText = ";";
            }
            else {
                audio.pause();
                info.innerText = "音樂暫停";
                play.innerText = "4";
            }
        }

        //調整音量
        function VolumeChange() {
            audio.volume = vol.value / 100;
        }

        //音樂停止播放
        function StopSong() {
            audio.pause();
            audio.currentTime = 0;
            play.innerText = "4";
            info.innerText = "音樂停止播放~~~~";
        }
    </script>
</body>
</html>