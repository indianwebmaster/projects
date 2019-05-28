# php/cricket/web
This project originally started as php/ipl. However, it was upgraded to allow any tournament, and allow user self management.

To add a new tournament (e.g. t20_worldcup2020)
1. cp index_template.php index_t20_worldcup2020.php
2. cp -r template t20_worldcup2020
3. Edit MConfig.php and create a new function "initvars_t20_worldcup2020()" and call it in "initvars()"
4. Edit MRules.php and create a new function "showRules_t20_worldcup2020()" and call it in "showRules()"
5. Edit the data files in folder t20_worldcup2020. games.dat, teams.dat, users.dat and bonus.dat plus bonus2.dat.
6. Update pictures in folder t20_worldcup2020/img (bg.jpg). Add any other pictures (such as sched1.jpg).
