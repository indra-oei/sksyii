CREATE OR REPLACE PROCEDURE sp_indra_transaction_insert
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_order_id             IN indra_transaction.order_id%TYPE,
    in_total                IN indra_transaction.total%TYPE,
    in_service_amount       IN indra_transaction.service_amount%TYPE,
    in_tax_amount           IN indra_transaction.tax_amount%TYPE,
    in_grand_total          IN indra_transaction.grand_total%TYPE,
    in_contact_id           IN indra_transaction.create_by%TYPE,
    in_owner_id             IN indra_transaction.owner_id%TYPE
) IS
    v_count_order           NUMBER;
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_order
    FROM
        indra_order io
    WHERE
        io.id = in_order_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;
    
    IF v_count_order = 0 THEN
        out_num := 1;
        out_str := 'Order not found';

        RETURN;
    END IF;


    INSERT INTO indra_transaction
    (
        id,
        order_id,
        total,
        service_amount,
        tax_amount,
        grand_total,
        create_by,
        owner_id
    ) 
    VALUES
    (
        seq_indra_transaction_id.NEXTVAL,
        in_order_id,
        in_total,
        in_service_amount,
        in_tax_amount,
        in_grand_total,
        in_contact_id,
        in_owner_id
    );

    SELECT
        'INSERT TRANSACTION',
        'Order No: ' || io.order_no || CHR(10) ||
        'Total: ' || in_total || CHR(10) ||
        'Service Amount: ' || in_service_amount || CHR(10) ||
        'Tax Amount: Rp ' || in_tax_amount || CHR(10) ||
        'Grand Total: ' || in_grand_total
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_order io
    WHERE
        io.id = in_order_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;

    sp_indra_history_insert
    (
        out_num,
        out_str,
        v_action_type,
        v_action_desc,
        in_contact_id,
        in_owner_id
    );
END;
/
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_TRANSACTION_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_transaction_update
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_order_id             IN indra_transaction.order_id%TYPE,
    in_contact_id           IN indra_transaction.create_by%TYPE,
    in_owner_id             IN indra_transaction.owner_id%TYPE
) IS
    v_count_exists          NUMBER;
    v_payment_date          DATE;
    v_order_status          VARCHAR2(10);
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_exists
    FROM
        indra_order io
    WHERE
        io.id = in_order_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;
    
    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Order does not exist';

        RETURN;
    END IF;

    SELECT
        io.order_status
    INTO
        v_order_status
    FROM
        indra_order io
    WHERE
        io.id = in_order_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;
    
    IF v_order_status = 'Cancel' THEN
        v_payment_date := NULL;
    ELSIF v_order_status = 'Pay' THEN
        v_payment_date := SYSDATE;
    END IF;

    UPDATE indra_transaction
    SET
        payment_date = v_payment_date,
        status = 0,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        order_id = in_order_id
        AND owner_id = in_owner_id
        AND status = 1;
END;
/
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_TRANSACTION_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_transaction_delete
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_order_id             IN indra_transaction.order_id%TYPE,
    in_contact_id           IN indra_transaction.create_by%TYPE,
    in_owner_id             IN indra_transaction.owner_id%TYPE
) IS
    v_count_exists          NUMBER;
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_exists
    FROM
        indra_order io
    WHERE
        io.id = in_order_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;
    
    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Order Detail does not exist';

        RETURN;
    END IF;

    DELETE FROM indra_transaction
    WHERE
        order_id = in_order_id;
END;
/
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ORDER_DELETE';