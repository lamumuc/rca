
New race
> RM        Add new "Round-Mark" race
> FD        Add new "Fixed-Distance" race , classes with PY value will be included
> []        Set offset time (Seconds) for display only, not affecting system time

Current race
> [Del]     Delete the current race and all the marked time
> [Mode]    Switch the race mode of the race
            x Cannot be changed after race started+5min 
> [Route]   Edit the route of the race of the Group
            x Cannot be changed after any sail in the group finished
> [Start]   Set the Start time of each group, -10min ~ +30min from now
            Cancel the Start time by right click
            > count down shown if race not yet started, lap time shown if the race started
            x Cannot be changed after race started+5min
> [Limits]  Set Time Limits for Current Race
            (minutes counting from Start time)
            x Cannot be changed after first sail finished
> [CutOff]  Set Cutoff Time for Class
            (minutes counting from the first Sail's Finish time)
            x Cannot be changed after first sail finished


Code Abbreviation
-- manual input
> DNC  Did not come
> OCS  On course side before starting signal and failed to restart, or broke rule 30.1 
> DNS  Did not start
> RET  Retired
> DSQ  Disqualification 
> DNE  Disqualification not excludable
-- computed by timestamps
> NSC  Not sail the course (Sailed course did not match race course)
> DNF  Did not finish (Did not pass Finish or Finish time > Limits / CutOff)


anem clownfish
dory findsnemo
goby waterfall
mink thickcoat
mola sunnyfish
orca nightsnow
otar shellfree
puff toxicball
seal magicdeep
tern abovewave


stamp[r,m,s,t]
DONE- [r= r,m=91,s=gp] > store start time of each race / class
DONE- [r= r,m=99,s=s]  > store finish time for each sail in each race
    - [r=5r,m=99,s=s]  > store py corrected time for each sail in each FD race
DONE- [r=50,m=50,s="offset"]         > store offset time of Unixtime & UTCtime for display
DONE- [r= r,m=50,s="cutoff",t=20]    > store cutoff time for each race
DONE- [r= r,m=50,s="limits",t=50/90] > store limits time for each race

result[r,m,rank,rank_py,score,score_py]
 XXX- [r=5r] > store py rank and score
 XXX- [r=95] > overall py score and rank
             > rank of [r=5r] scores
 XXX- [r=99] > overall score and rank
             > [score] = sum of scores-min
             > [rank] = rank of [r=99] scores
