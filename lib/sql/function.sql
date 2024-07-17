CREATE OR REPLACE FUNCTION fn_indra_generate_order_number
RETURN VARCHAR2 IS
    v_order_number      VARCHAR2(6);
    v_count_exists      NUMBER := 0;
    v_char              VARCHAR2(1);
BEGIN
    LOOP
        FOR i IN 1..6 LOOP
            -- Generate a random character between '0'-'9' (ASCII 48-57) or 'A'-'Z' (ASCII 65-90)
            IF DBMS_RANDOM.VALUE(0, 1) < 0.5 THEN
                v_char := CHR(TRUNC(DBMS_RANDOM.VALUE(48, 58)));  -- ASCII range for '0'-'9'
            ELSE
                v_char := CHR(TRUNC(DBMS_RANDOM.VALUE(65, 91)));  -- ASCII range for 'A'-'Z'
            END IF;
            
            v_order_number := v_order_number || v_char;
        END LOOP;

        SELECT
            COUNT(1)
        INTO
            v_count_exists
        FROM
            indra_order io
        WHERE
            io.order_no = v_order_number;

        IF v_count_exists = 0 THEN
            EXIT;
        END IF;
    END LOOP;

    RETURN v_order_number;
END;
/
SELECT * FROM USER_ERRORS WHERE NAME = 'FN_INDRA_GENERATE_ORDER_NUMBER';

CREATE OR REPLACE FUNCTION fn_indra_generate_table_number
RETURN NUMBER
IS
    v_table_num         NUMBER;
    v_count_exists      NUMBER := 0;
BEGIN
    LOOP
        v_table_num := TRUNC(DBMS_RANDOM.VALUE(1, 16)); -- DBMS_RANDOM.VALUE generates a number between low and high (both inclusive)
        
        SELECT
            COUNT(1)
        INTO
            v_count_exists
        FROM
            indra_order io
        WHERE
            io.table_no = v_table_num
            AND io.order_status = 'New'
            AND io.status = 1;
        
        IF v_count_exists = 0 THEN
            EXIT;
        END IF;
    END LOOP;

    RETURN v_table_num;
END;
/
