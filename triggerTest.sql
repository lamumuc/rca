-- Active: 1690078383828@@127.0.0.1@3306@rca


DROP TRIGGER IF EXISTS `newRoute_newResult`;
CREATE DEFINER=`root`@`localhost` TRIGGER `newRoute_newResult` AFTER INSERT ON `route` FOR EACH ROW 
BEGIN
IF (NEW.r != 0) THEN
    -- create result [r,s,code,rank,rank_py] for every r
    INSERT IGNORE INTO result (r,s,code,`rank`,`rank_py`)
    SELECT DISTINCT route.r AS r, entry.s AS s, "DNF" AS code, (SELECT COUNT(s) FROM entry WHERE entry.cate = grade.cate) + 1 AS `rank`, 
    IF(route.mode != 'FD',0,(SELECT COUNT(s) FROM entry JOIN grade ON grade.cate = entry.cate WHERE grade.py > 0) + 1) AS `rank_py`
    FROM route
    JOIN grade ON route.gp = grade.gp
    JOIN entry ON grade.cate = entry.cate
    WHERE route.r = NEW.r and grade.gp = NEW.gp;

    -- create 'cutoff' and 'limits' for every r
    INSERT INTO stamp (r, m, s, t)
    SELECT NEW.r, 50, 'bCutoff', 1200000 FROM dual
    WHERE NOT EXISTS (SELECT 1 FROM stamp WHERE r = NEW.r AND s = 'bCutoff');
        
    INSERT INTO stamp (r, m, s, t)
    SELECT NEW.r, 50, 'bLimits', 3000000 FROM dual
    WHERE NOT EXISTS (SELECT 1 FROM stamp WHERE r = NEW.r AND s = 'bLimits');
END IF;
END;


/* -- no need 
    -- create final result [99,s]
    INSERT IGNORE INTO result (r, s)
    SELECT 99 AS r, entry.s AS s
    FROM route
    JOIN grade ON route.gp = grade.gp
    JOIN entry ON grade.cate = entry.cate
    WHERE route.r = NEW.r AND grade.gp = NEW.gp;
 */












DROP TRIGGER IF EXISTS `newStamp99_updateResult`;
CREATE DEFINER=`root`@`localhost` TRIGGER `newStamp99_updateResult` 
AFTER INSERT ON `stamp` FOR EACH ROW 
BEGIN
IF ((SELECT code FROM result WHERE r = NEW.r AND s = NEW.s) IN ('DNF','NSC','FIN')) THEN
    CALL updateRank(NEW.r,NEW.s);
END IF;
END;





DROP TRIGGER IF EXISTS `delStamp99_updateResult`;
CREATE DEFINER=`root`@`localhost` TRIGGER `delStamp99_updateResult` AFTER DELETE ON `stamp` FOR EACH ROW 
BEGIN 
IF ((SELECT code FROM result WHERE r = OLD.r AND s = OLD.s) IN ('FIN')) THEN
    CALL updateRank(OLD.r,OLD.s);
END IF;
END;

/* -- code on PROD
WHILE @i <= LENGTH(@gppath) AND @j <= LENGTH(@sailed) DO
    IF SUBSTRING(@gppath, @i, 1) = SUBSTRING(@sailed, @j, 1) THEN
        SET @i = @i + 1;
    END IF;
    SET @j = @j + 1;
END WHILE; */



DROP PROCEDURE IF EXISTS `updateCode`;
CREATE PROCEDURE `updateCode`(IN `r1` INT, IN `s1` VARCHAR(50)) 
BEGIN
-- get finish time
    SET @tfinish = (SELECT MAX(t) AS max_t FROM stamp WHERE r=r1 AND m=99 AND s = s1);
-- check finish time - start time < cutoff and limits
    IF @tfinish != 0 THEN
        SET @cutoff = (SELECT t FROM stamp WHERE r=r1 AND s='bCutoff');
        SET @limits = (SELECT t FROM stamp WHERE r=r1 AND s='bLimits');
        SET @tstart = (SELECT t FROM stamp WHERE r=r1 AND m=91 AND s=(SELECT gp FROM grade WHERE cate=(SELECT cate FROM entry WHERE s=s1)));
        SET @tfirst = (SELECT t FROM stamp WHERE r=r1 AND m=99 AND s=(SELECT cate FROM entry WHERE s=s1));

        IF (@tfinish - @tstart > @limits) OR (@tfinish - @tfirst > @cutoff) THEN
            UPDATE result SET code = 'DNF' WHERE r = r1 AND s = s1;
        ELSE
            SET @gppath = (SELECT route.path FROM route JOIN grade ON route.gp = grade.gp JOIN entry ON grade.cate = entry.cate WHERE route.r=r1 AND entry.s = s1);
            SET @sailed = (SELECT GROUP_CONCAT(IF(m = 99, 'F', CAST(m AS CHAR)) ORDER BY t ASC SEPARATOR '') AS sailed FROM stamp WHERE r = r1 AND s = s1);
            SET @i = 1;
            SET @j = 1;

            WHILE @i <= LENGTH(@gppath) AND @j <= LENGTH(@sailed) DO
                SET @char_a = SUBSTR(@gppath, @i, 1);
                SET @char_b = SUBSTR(@sailed, @j, 1);
                IF @char_a COLLATE utf8mb4_general_ci = @char_b COLLATE utf8mb4_general_ci THEN
                    SET @i = @i + 1;
                END IF;
                SET @j = @j + 1;
            END WHILE;

            IF @i <= LENGTH(@gppath) THEN
                UPDATE result SET code = 'NSC' WHERE r = r1 AND s = s1;
            ELSE
                UPDATE result SET code = 'FIN' WHERE r = r1 AND s = s1;
            END IF;
        END IF;
    ELSE
        UPDATE result SET code = 'DNF' WHERE r = r1 AND s = s1;
    END IF;
END;


/*
            SET @sailed = (SELECT GROUP_CONCAT(IF(m=99,'F',m) SEPARATOR '') FROM stamp WHERE r = r1 AND s = s1 ORDER BY stamp.t ASC);*/




DROP PROCEDURE IF EXISTS `updateRank`;
CREATE PROCEDURE `updateRank`(IN `r1` INT, IN `s1` VARCHAR(50)) 
BEGIN
-- check DNF/NSC/FIN
    IF ((SELECT code FROM result WHERE r = r1 AND s = s1) IN ('DNF','NSC','FIN')) THEN
        CALL updateCode(r1,s1);
    END IF;

-- update rank of r1 s1 if code!='FIN'
    UPDATE result
    SET `rank` = (SELECT COUNT(s) FROM entry WHERE cate=(SELECT cate FROM entry WHERE s = s1)) + 1
    WHERE r=r1 AND s=s1 AND code != 'FIN';

-- update rank of r1 all s in cate with 'FIN' order by t
    UPDATE result
    JOIN stamp ON stamp.r = result.r AND stamp.s = result.s AND result.code = 'FIN' AND stamp.m=99 
    JOIN (SELECT s, MAX(t) AS max_t FROM stamp
    WHERE r = r1 AND m = 99 AND s IN (SELECT s FROM entry WHERE cate = (SELECT cate FROM entry WHERE s = s1))
    GROUP BY s) AS max_stamp ON stamp.s = max_stamp.s AND stamp.t = max_stamp.max_t
    SET result.`rank` = (
            SELECT COUNT(*) FROM (
                SELECT stamp.r, stamp.s, stamp.t, result.code FROM result
                JOIN stamp ON stamp.r = result.r AND stamp.s = result.s AND result.code = 'FIN' AND stamp.m = 99
                JOIN (SELECT s, MAX(t) AS max_t FROM stamp
                WHERE r = r1 AND m = 99 AND s IN (SELECT s FROM entry WHERE cate = (SELECT cate FROM entry WHERE s = s1))
                GROUP BY s) AS max_stamp ON stamp.s = max_stamp.s AND stamp.t = max_stamp.max_t
                WHERE stamp.r = r1
            ) t2
            WHERE t2.t < stamp.t) + 1
    WHERE result.r = r1 AND stamp.s IN (SELECT s FROM entry WHERE cate=(SELECT cate FROM entry WHERE s=s1));

-- update rank_py with code='FIN' sort by t_py, add up all rank_py save as score_py [r=95,s all], update rank by sorting [r=95,s all,code='FIN'] order by score_py
-- compute t_py=(t-t_start)/py*1000, 
-- store as stamp [r+50,m=99,s,t_py]

-- compute nett @@@@
--    CALL updateNett(s1);

END;


/* -- no need
-- update score as sum of current and prev ranks
-- version 0
    UPDATE result
    SET score = (SELECT SUM(`rank`) FROM result r0 WHERE r0.s = result.s AND r0.r <= result.r)
    WHERE r <= r1 AND s IN (SELECT s FROM entry WHERE cate=(SELECT cate FROM entry WHERE s=s1));
-- version in PROD
    UPDATE result SET score = ( SELECT SUM(result2.`rank`) FROM (
        SELECT result1.`rank` FROM result result1 WHERE result1.s = result.s AND result1.r <= result.r) result2); */






ALTER IGNORE TABLE result ADD CONSTRAINT unique_r_s UNIQUE (r, s);
ALTER IGNORE TABLE route ADD CONSTRAINT unique_r_gp UNIQUE (r, gp);





/* DROP TRIGGER IF EXISTS `newRank_updateNett`;
CREATE DEFINER=`root`@`localhost` TRIGGER `newRank_updateNett` AFTER INSERT ON `result` FOR EACH ROW 
BEGIN
IF (NEW.r < 50) THEN
    CALL updateNett(NEW.s);
END IF;
END;
 */

DROP TRIGGER IF EXISTS `updRank_updateNett`;
CREATE DEFINER=`root`@`localhost` TRIGGER `updRank_updateNett` AFTER UPDATE ON `result` FOR EACH ROW 
BEGIN 
IF (NEW.r < 50) THEN
    CALL updateNett(NEW.s);
END IF;
END;

/* 
DROP TRIGGER IF EXISTS `delRank_updateNett`;
CREATE DEFINER=`root`@`localhost` TRIGGER `delRank_updateNett` AFTER DELETE ON `result` FOR EACH ROW 
BEGIN 
IF (OLD.r < 50) THEN
    CALL updateNett(OLD.s);
END IF;
END;

 */



DROP PROCEDURE IF EXISTS `updateNett`;
CREATE PROCEDURE `updateNett`(IN `s1` VARCHAR(50)) 
BEGIN
    SET @rC = (SELECT MAX(r) FROM route WHERE r < 50);
    SET @rE = FLOOR((@rC - 4) / 8);
    SET @nett1 = (SELECT SUM(`rank`) FROM result WHERE s = '51' AND r < 50);
    
    -- Loop until rE becomes 0 @@@@ not successful
    WHILE @rE > 0 DO
        -- Find the row with the maximum rank, smallest r, and code not beginning with "E" where s = s1
        SET @r0 = (SELECT r FROM result WHERE s = s1 AND NOT code LIKE 'E%' ORDER BY `rank` DESC, r ASC LIMIT 1);
        
        IF @r0 IS NOT NULL THEN
        	SET @rank0 = (SELECT `rank` FROM result WHERE r = 2 AND s = '51');
            UPDATE result SET code = CONCAT('E', code) WHERE r = @r0 AND s = s1;
            SET @nett1 = @nett1 - @rank0;
            SET @rE = @rE - 1;
        ELSE
            UPDATE entry SET nett_py = @rE WHERE s = s1;
            SET @rE = 0;
        END IF;
    END WHILE;
    
    UPDATE entry SET nett = @nett1 WHERE s = s1;
END;









B185F1

SELECT * FROM stamp WHERE s='B185F1NEW';




