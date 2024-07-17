CREATE OR REPLACE PROCEDURE sp_indra_history_insert
(
    out_num             OUT NUMBER,
    out_str             OUT VARCHAR2,
    in_action_type      IN indra_history.action_type%TYPE,
    in_action_desc      IN indra_history.action_type%TYPE,
    in_contact_id       IN indra_history.create_by%TYPE,
    in_owner_id         IN indra_history.owner_id%TYPE
) IS
BEGIN
    out_num := 0;
    out_str := 'Success';

    INSERT INTO indra_history
    (
        id,
        action_type,
        action_desc,
        create_by,
        owner_id
    )
    VALUES
    (
        seq_indra_history_id.NEXTVAL,
        in_action_type,
        in_action_desc,
        in_contact_id,
        in_owner_id
    );
END;
/
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_HISTORY_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_user_insert
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_name                 IN indra_user.name%TYPE,
    in_email                IN indra_user.email%TYPE,
    in_password             IN indra_user.password%TYPE,
    in_contact_id           IN indra_user.create_by%TYPE,
    in_owner_id             IN indra_user.owner_id%TYPE
) IS
    v_count_user            NUMBER;
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_user
    FROM
        indra_user iu
    WHERE
        UPPER(iu.email) = UPPER(in_email)
        AND iu.owner_id = in_owner_id
        AND iu.status = 1;

    IF v_count_user > 0 THEN
        out_num := 1;
        out_str := 'Email already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for user name is 128';

        RETURN;
    END IF;

    IF LENGTH(in_email) > 256 THEN
        out_num := 1;
        out_str := 'Maximum character for email is 256';

        RETURN;
    END IF;

    INSERT INTO indra_user
    (
        id,
        name,
        email,
        password,
        create_by,
        owner_id
    ) 
    VALUES
    (
        seq_indra_user_id.NEXTVAL,
        in_name,
        in_email,
        in_password,
        in_contact_id,
        in_owner_id
    );

    SELECT
        'INSERT USER',
        'Name: ' || in_name || CHR(10) ||
        'Email: ' || in_email
    INTO
        v_action_type,
        v_action_desc
    FROM
        DUAL;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_USER_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_user_update
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_user.id%TYPE,
    in_name                 IN indra_user.name%TYPE,
    in_email                IN indra_user.email%TYPE,
    in_password             IN indra_user.password%TYPE,
    in_contact_id           IN indra_user.create_by%TYPE,
    in_owner_id             IN indra_user.owner_id%TYPE
) IS
    v_count_exists          NUMBER;
    v_count_user            NUMBER;
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
        indra_user iu
    WHERE
        iu.id = in_id;

    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;
    

    SELECT
        COUNT(1)
    INTO
        v_count_user
    FROM
        indra_user iu
    WHERE
        iu.id != id
        AND UPPER(iu.email) = UPPER(in_email)
        AND iu.owner_id = in_owner_id
        AND iu.status = 1;

    IF v_count_user > 0 THEN
        out_num := 1;
        out_str := 'Email already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for user name is 128';

        RETURN;
    END IF;

    IF LENGTH(in_email) > 256 THEN
        out_num := 1;
        out_str := 'Maximum character for email is 256';

        RETURN;
    END IF;

    SELECT
        'UPDATE USER',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Name: ' || iu.name || CHR(10) ||
        'Email: ' || iu.email
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_user iu
    WHERE
        iu.id = in_id
        AND iu.owner_id = 1
        AND iu.status = 1;

    UPDATE indra_user
    SET
        name = in_name,
        email = in_email,
        password = in_password,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;

    SELECT
        v_action_desc || CHR(10) || CHR(10) ||
        'INSERT USER' || CHR (10) ||
        'Name: ' || in_name || CHR(10) ||
        'Email: ' || in_email
    INTO
        v_action_desc
    FROM
        DUAL;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_USER_UPDATE';

CREATE OR REPLACE PROCEDURE sp_indra_user_delete
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_user.id%TYPE,
    in_contact_id           IN indra_user.create_by%TYPE,
    in_owner_id             IN indra_user.owner_id%TYPE
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
        indra_user iu
    WHERE
        iu.id = in_id
        AND iu.owner_id = in_owner_id
        AND iu.status = 1;

    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;

    SELECT
        'DELETE USER',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Name: ' || iu.name || CHR(10) ||
        'Email: ' || iu.email
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_user iu
    WHERE
        iu.id = in_id
        AND iu.owner_id = 1
        AND iu.status = 1;

    UPDATE indra_user
    SET
        status = 0,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_USER_UPDATE';

CREATE OR REPLACE PROCEDURE sp_indra_category_insert
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_name                 IN indra_category.name%TYPE,
    in_contact_id           IN indra_category.create_by%TYPE,
    in_owner_id             IN indra_category.owner_id%TYPE
) IS
    v_count_category        NUMBER;
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_category
    FROM
        indra_category ic
    WHERE
        UPPER(ic.name) = UPPER(in_name)
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

    IF v_count_category > 0 THEN
        out_num := 1;
        out_str := 'Category name already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for category name is 128';

        RETURN;
    END IF;

    INSERT INTO indra_category
    (
        id,
        name,
        create_by,
        owner_id
    ) 
    VALUES
    (
        seq_indra_category_id.NEXTVAL,
        in_name,
        in_contact_id,
        in_owner_id
    );

    SELECT
        'INSERT CATEGORY',
        'Name: ' || in_name
    INTO
        v_action_type,
        v_action_desc
    FROM
        DUAL;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_CATEGORY_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_category_update
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_category.id%TYPE,
    in_name                 IN indra_category.name%TYPE,
    in_contact_id           IN indra_category.create_by%TYPE,
    in_owner_id             IN indra_category.owner_id%TYPE
) IS
    v_count_exists          NUMBER;
    v_count_category        NUMBER;
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
        indra_category ic
    WHERE
        ic.id = in_id
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;
    
    SELECT
        COUNT(1)
    INTO
        v_count_category
    FROM
        indra_category ic
    WHERE
        ic.id != id
        AND UPPER(ic.name) = UPPER(in_name)
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

    IF v_count_category > 0 THEN
        out_num := 1;
        out_str := 'Category name already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for category name is 128';

        RETURN;
    END IF;

    SELECT
        'UPDATE CATEGORY',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Name: ' || ic.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_category ic
    WHERE
        ic.id = in_id
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

    UPDATE indra_category
    SET
        name = in_name,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;

    SELECT
        v_action_desc || CHR(10) || CHR(10) ||
        'NEW DATA' || CHR(10) || CHR(10) ||
        'Name: ' || in_name
    INTO
        v_action_desc
    FROM
        DUAL;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_CATEGORY_UPDATE';

CREATE OR REPLACE PROCEDURE sp_indra_category_delete
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_category.id%TYPE,
    in_contact_id           IN indra_category.create_by%TYPE,
    in_owner_id             IN indra_category.owner_id%TYPE
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
        indra_category ic
    WHERE
        ic.id = in_id
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;

    SELECT
        'DELETE USER',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Name: ' || ic.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_category ic
    WHERE
        ic.id = in_id
        AND ic.owner_id = 1
        AND ic.status = 1;

    UPDATE indra_category
    SET
        status = 0,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_CATEGORY_DELETE';

CREATE OR REPLACE PROCEDURE sp_indra_subcategory_insert
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_category_id          IN indra_subcategory.category_id%TYPE,
    in_name                 IN indra_subcategory.name%TYPE,
    in_contact_id           IN indra_subcategory.create_by%TYPE,
    in_owner_id             IN indra_subcategory.owner_id%TYPE
) IS
    v_count_category        NUMBER;
    v_count_subcategory     NUMBER;
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_subcategory
    FROM
        indra_subcategory isc
    WHERE
        UPPER(isc.name) = UPPER(in_name)
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

    IF v_count_subcategory > 0 THEN
        out_num := 1;
        out_str := 'Sub category name already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for sub category name is 128';

        RETURN;
    END IF;

    SELECT
        COUNT(1)
    INTO
        v_count_category
    FROM
        indra_category ic
    WHERE
        ic.id = in_category_id
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

    IF v_count_category = 0 THEN
        out_num := 1;
        out_str := 'Category does not exist';

        RETURN;
    END IF;

    INSERT INTO indra_subcategory
    (
        id,
        category_id,
        name,
        create_by,
        owner_id
    ) 
    VALUES
    (
        seq_indra_subcategory_id.NEXTVAL,
        in_category_id,
        in_name,
        in_contact_id,
        in_owner_id
    );

    SELECT
        'INSERT SUB CATEGORY',
        'Name: ' || in_name || CHR(10) ||
        'Category: ' || ic.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_category ic
    WHERE
        ic.id = in_category_id
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_SUBCATEGORY_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_subcategory_update
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_subcategory.id%TYPE,
    in_category_id          IN indra_subcategory.category_id%TYPE,
    in_name                 IN indra_subcategory.name%TYPE,
    in_contact_id           IN indra_subcategory.create_by%TYPE,
    in_owner_id             IN indra_subcategory.owner_id%TYPE
) IS
    v_count_exists          NUMBER;
    v_count_category        NUMBER;
    v_count_subcategory     NUMBER;
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
        indra_subcategory isc
    WHERE
        isc.id = in_id
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_subcategory
    FROM
        indra_subcategory isc
    WHERE
        isc.id != in_id
        AND UPPER(isc.name) = UPPER(in_name)
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

    IF v_count_subcategory > 0 THEN
        out_num := 1;
        out_str := 'Sub category name already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for sub category name is 128';

        RETURN;
    END IF;
    
    SELECT
        COUNT(1)
    INTO
        v_count_category
    FROM
        indra_category ic
    WHERE
        ic.id = in_category_id
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

    IF v_count_category = 0 THEN
        out_num := 1;
        out_str := 'Category does not exist';

        RETURN;
    END IF;

    SELECT
        'UPDATE SUB CATEGORY',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Name: ' || isc.name || CHR(10) ||
        'Category: ' || ic.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_subcategory isc,
        indra_category ic
    WHERE
        isc.category_id = ic.id
        AND isc.id = in_id
        AND isc.owner_id = in_owner_id
        AND ic.owner_id = in_owner_id
        AND isc.status = 1
        AND ic.status = 1;

    UPDATE indra_subcategory
    SET
        category_id = in_category_id,
        name = in_name,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;

    SELECT
        v_action_desc || CHR(10) || CHR(10) ||
        'NEW DATA' || CHR(10) || CHR(10) ||
        'Name: ' || in_name || CHR(10) ||
        'Category: ' || ic.name
    INTO
        v_action_desc
    FROM
        indra_category ic
    WHERE
        ic.id = in_category_id
        AND ic.owner_id = in_owner_id
        AND ic.status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_SUBCATEGORY_UPDATE';

CREATE OR REPLACE PROCEDURE sp_indra_subcategory_delete
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_subcategory.id%TYPE,
    in_contact_id           IN indra_subcategory.create_by%TYPE,
    in_owner_id             IN indra_subcategory.owner_id%TYPE
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
        indra_subcategory isc
    WHERE
        isc.id = in_id
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;

    SELECT
        'DELETE USER',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Name: ' || isc.name || CHR(10) ||
        'Category: ' || ic.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_subcategory isc,
        indra_category ic
    WHERE
        isc.category_id = ic.id
        AND isc.id = in_id
        AND isc.owner_id = in_owner_id
        AND ic.owner_id = in_owner_id
        AND isc.status = 1
        AND ic.status = 1;

    UPDATE indra_subcategory
    SET
        status = 0,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_SUBCATEGORY_DELETE';

CREATE OR REPLACE PROCEDURE sp_indra_item_insert
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_subcategory_id       IN indra_item.subcategory_id%TYPE,
    in_name                 IN indra_item.name%TYPE,
    in_description          IN indra_item.description%TYPE,
    in_price                IN indra_item.price%TYPE,
    in_valid_until          IN indra_item.valid_until%TYPE,
    in_contact_id           IN indra_item.create_by%TYPE,
    in_owner_id             IN indra_item.owner_id%TYPE
) IS
    v_count_subcategory     NUMBER;
    v_count_item            NUMBER;
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    SELECT
        COUNT(1)
    INTO
        v_count_item
    FROM
        indra_item it
    WHERE
        UPPER(it.name) = UPPER(in_name)
        AND it.owner_id = in_owner_id
        AND it.status = 1;

    IF v_count_item > 0 THEN
        out_num := 1;
        out_str := 'Food name already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for food name is 128';

        RETURN;
    END IF;

    IF LENGTH(in_description) > 256 THEN
        out_num := 1;
        out_str := 'Maximum character for food description is 256';

        RETURN;
    END IF;

    IF in_price < 0 AND in_price > 10000000 THEN
        out_num := 1;
        out_str := 'Price needs to be between 0 and 10.000.000';

        RETURN;
    END IF;

    SELECT
        COUNT(1)
    INTO
        v_count_subcategory
    FROM
        indra_subcategory isc
    WHERE
        isc.id = in_subcategory_id
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

    IF v_count_subcategory <= 0 THEN
        out_num := 1;
        out_str := 'Subcategory not found';

        RETURN;
    END IF;

    INSERT INTO indra_item
    (
        id,
        subcategory_id,
        name,
        description,
        price,
        valid_until,
        create_by,
        owner_id
    ) 
    VALUES
    (
        seq_indra_item_id.NEXTVAL,
        in_subcategory_id,
        in_name,
        in_description,
        in_price,
        in_valid_until,
        in_contact_id,
        in_owner_id
    );

    SELECT
        'INSERT FOOD',
        'Food Name: ' || in_name || CHR(10) ||
        'Description: ' || in_description || CHR(10) ||
        'Price: Rp ' || in_price || CHR(10) ||
        'Valid Until: ' || TO_CHAR(in_valid_until, 'DD/MM/YYYY') || CHR(10) ||
        'Sub Category: ' || isc.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_subcategory isc
    WHERE
        isc.id = in_subcategory_id
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ITEM_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_item_update
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_item.id%TYPE,
    in_subcategory_id       IN indra_item.subcategory_id%TYPE,
    in_name                 IN indra_item.name%TYPE,
    in_description          IN indra_item.description%TYPE,
    in_price                IN indra_item.price%TYPE,
    in_valid_until          IN indra_item.valid_until%TYPE,
    in_contact_id           IN indra_item.create_by%TYPE,
    in_owner_id             IN indra_item.owner_id%TYPE
) IS
    v_count_exists          NUMBER;
    v_count_subcategory     NUMBER;
    v_count_item            NUMBER;
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
        indra_item it
    WHERE
        it.id = in_id;
    
    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;

    SELECT
        COUNT(1)
    INTO
        v_count_subcategory
    FROM
        indra_subcategory isc
    WHERE
        isc.id = in_subcategory_id
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

    IF v_count_subcategory = 0 THEN
        out_num := 1;
        out_str := 'Sub Category not found';

        RETURN;
    END IF;

    SELECT
        COUNT(1)
    INTO
        v_count_item
    FROM
        indra_item it
    WHERE
        it.id != in_id
        AND UPPER(it.name) = in_name
        AND it.owner_id = in_owner_id
        AND it.status = 1;
    
    IF v_count_item > 0 THEN
        out_num := 1;
        out_num := 'Food name already exists';

        RETURN;
    END IF;

    IF LENGTH(in_name) > 128 THEN
        out_num := 1;
        out_str := 'Maximum character for food name is 128';

        RETURN;
    END IF;

    IF LENGTH(in_description) > 256 THEN
        out_num := 1;
        out_str := 'Maximum character for food description is 200';

        RETURN;
    END IF;

    IF in_price < 0 AND in_price > 10000000 THEN
        out_num := 1;
        out_str := 'Price needs to be between 0 and 10.000.000';

        RETURN;
    END IF;

    SELECT
        'UPDATE FOOD',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Food Name: ' || it.name || CHR(10) ||
        'Description: ' || it.description || CHR(10) ||
        'Price: Rp ' || it.price || CHR(10) ||
        'Valid Until: ' || it.valid_until || CHR(10) ||
        'Sub Category: ' || isc.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_item it,
        indra_subcategory isc
    WHERE 
        it.subcategory_id = isc.id
        AND it.id = in_id
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;
    
    UPDATE indra_item
    SET
        name = in_name,
        description = in_description,
        price = in_price,
        valid_until = TO_DATE(in_valid_until, 'DD/MM/YYYY'),
        subcategory_id = in_subcategory_id,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;
    
    SELECT
        v_action_desc || CHR(10) || CHR(10) ||
        'NEW DATA' || CHR(10) || CHR(10) ||
        'Food Name: ' || in_name || CHR(10) ||
        'Description: ' || in_description || CHR(10) ||
        'Price: Rp ' || in_price || CHR(10) ||
        'Valid Until: ' || in_valid_until || CHR(10) ||
        'Sub Category: ' || isc.name
    INTO
        v_action_desc
    FROM
        indra_subcategory isc
    WHERE
        isc.id = in_subcategory_id
        AND isc.owner_id = in_owner_id
        AND isc.status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ITEM_UPDATE';

CREATE OR REPLACE PROCEDURE sp_indra_item_delete
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_item.id%TYPE,
    in_contact_id           IN indra_item.create_by%TYPE,
    in_owner_id             IN indra_item.owner_id%TYPE
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
        indra_item it
    WHERE
        it.id = in_id
        AND it.owner_id = in_owner_id
        AND it.status = 1;
    
    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Data not found';

        RETURN;
    END IF;

    SELECT
        'DELETE FOOD',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Food Name: ' || it.name || CHR(10) ||
        'Description: ' || it.description || CHR(10) ||
        'Price: Rp ' || it.price || CHR(10) ||
        'Valid Until: ' || it.valid_until || CHR(10) ||
        'Sub Category: ' || isc.name
    INTO
        v_action_type,
        v_action_desc
    FROM
        indra_item it,
        indra_subcategory isc
    WHERE
        it.id = in_id
        AND it.subcategory_id = isc.id
        AND it.owner_id = isc.owner_id
        AND it.owner_id = in_owner_id
        AND it.status = 1
        AND isc.status = 1;

    UPDATE indra_item
    SET
        status = 0,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;
    
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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ITEM_DELETE';

CREATE OR REPLACE PROCEDURE sp_indra_order_insert
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    out_order_no            OUT VARCHAR2,
    in_customer_name        IN indra_order.customer_name%TYPE,
    in_customer_sex         IN indra_order.customer_sex%TYPE,
    in_table_no             IN indra_order.table_no%TYPE,
    in_total_customer       IN indra_order.total_customer%TYPE,
    in_contact_id           IN indra_order.create_by%TYPE,
    in_owner_id             IN indra_order.owner_id%TYPE
) IS
    v_count_customer        NUMBER;
    v_action_type           indra_history.action_type%TYPE;
    v_action_desc           indra_history.action_desc%TYPE;
BEGIN
    out_num := 0;
    out_str := 'Success';

    -- Validation
    IF in_total_customer = 0 THEN
        out_num := 1;
        out_str := 'Customer must be at least 1';

        RETURN;
    END IF;

    SELECT
        fn_indra_generate_order_number()
    INTO
        out_order_no
    FROM
        DUAL;

    INSERT INTO indra_order
    (
        id,
        order_no,
        customer_name,
        customer_sex,
        table_no,
        total_customer,
        create_by,
        owner_id
    ) 
    VALUES
    (
        seq_indra_order_id.NEXTVAL,
        out_order_no,
        in_customer_name,
        in_customer_sex,
        in_table_no,
        in_total_customer,
        in_contact_id,
        in_owner_id
    );

    SELECT
        'INSERT ORDER',
        'Order No: ' || out_order_no || CHR(10) ||
        'Customer Name: ' || in_customer_name || CHR(10) ||
        'Customer Sex: ' || in_customer_sex || CHR(10) ||
        'Table No: ' || in_table_no || CHR(10) ||
        'Total Customer: ' || in_total_customer || CHR(10) ||
        'Order Status: ' || 'New'
    INTO
        v_action_type,
        v_action_desc
    FROM
        DUAL;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ORDER_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_order_update
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_order.id%TYPE,
    in_order_status         IN indra_order.order_status%TYPE,
    in_contact_id           IN indra_order.create_by%TYPE,
    in_owner_id             IN indra_order.owner_id%TYPE
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
        io.id = in_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;
    
    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Order data does not exist';

        RETURN;
    END IF;

    SELECT
        'UPDATE ORDER',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Customer Name: ' || io.customer_name || CHR(10) ||
        'Customer Sex: Rp ' || io.customer_sex || CHR(10) ||
        'Table No: ' || io.table_no || CHR(10) ||
        'Total Customer: ' || io.total_customer || CHR(10) ||
        'Order Status: ' || io.order_status
    INTO 
        v_action_type,
        v_action_desc
    FROM
        indra_order io
    WHERE
        io.id = in_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;

    UPDATE indra_order
    SET
        order_status = in_order_status,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;
    
    SELECT
        v_action_desc || CHR(10) ||
        'NEW DATA' || CHR(10) || CHR(10) ||
        'Customer Name: ' || io.customer_name || CHR(10) ||
        'Customer Sex: Rp ' || io.customer_sex || CHR(10) ||
        'Table No: ' || io.table_no || CHR(10) ||
        'Total Customer: ' || io.total_customer || CHR(10) ||
        'Order Status: ' || in_order_status
    INTO 
        v_action_desc
    FROM
        indra_order io
    WHERE
        io.id = in_id
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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ORDER_UPDATE';

CREATE OR REPLACE PROCEDURE sp_indra_order_delete
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_id                   IN indra_order.id%TYPE,
    in_contact_id           IN indra_order.create_by%TYPE,
    in_owner_id             IN indra_order.owner_id%TYPE
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
        io.id = in_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;
    
    IF v_count_exists = 0 THEN
        out_num := 1;
        out_str := 'Order data does not exist';

        RETURN;
    END IF;

    SELECT
        'DELETE ORDER',
        'OLD DATA' || CHR(10) || CHR(10) ||
        'Customer Name: ' || io.customer_name || CHR(10) ||
        'Customer Sex: Rp ' || io.customer_sex || CHR(10) ||
        'Table No: ' || io.table_no || CHR(10) ||
        'Total Customer: ' || io.total_customer || CHR(10) ||
        'Order Status: ' || io.order_status
    INTO 
        v_action_type,
        v_action_desc
    FROM
        indra_order io
    WHERE
        io.id = in_id
        AND io.owner_id = in_owner_id
        AND io.status = 1;

    UPDATE indra_order
    SET
        status = 0,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        id = in_id
        AND owner_id = in_owner_id
        AND status = 1;

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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ORDER_DELETE';

CREATE OR REPLACE PROCEDURE sp_indra_order_detail_insert
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_order_id             IN indra_order_detail.order_id%TYPE,
    in_item_name            IN indra_order_detail.item_name%TYPE,
    in_item_quantity        IN indra_order_detail.item_quantity%TYPE,
    in_item_price           IN indra_order_detail.item_price%TYPE,
    in_subtotal             IN indra_order_detail.subtotal%TYPE,
    in_notes                IN indra_order_detail.notes%TYPE,
    in_contact_id           IN indra_order_detail.create_by%TYPE,
    in_owner_id             IN indra_order_detail.owner_id%TYPE
) IS
    v_count_item            NUMBER;
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

    SELECT
        COUNT(1)
    INTO
        v_count_item
    FROM
        indra_item it
    WHERE
        UPPER(it.name) = UPPER(in_item_name)
        AND it.owner_id = in_owner_id
        AND it.status = 1;
    
    IF v_count_item = 0 THEN
        out_num := 1;
        out_str := 'Item does not exist';

        RETURN;
    END IF;

    IF LENGTH(in_notes) > 4000 THEN
        out_num := 1;
        out_str := 'Maximum character for notes is 4000 characters';

        RETURN;
    END IF;

    INSERT INTO indra_order_detail
    (
        id,
        order_id,
        item_name,
        item_quantity,
        item_price,
        subtotal,
        notes,
        create_by,
        owner_id
    ) 
    VALUES
    (
        seq_indra_order_detail_id.NEXTVAL,
        in_order_id,
        in_item_name,
        in_item_quantity,
        in_item_price,
        in_subtotal,
        in_notes,
        in_contact_id,
        in_owner_id
    );

    SELECT
        'INSERT ORDER DETAIL',
        'Order No: ' || io.order_no || CHR(10) ||
        'Item Name: ' || in_item_name || CHR(10) ||
        'Quantity: ' || in_item_quantity || CHR(10) ||
        'Item Price: Rp ' || in_item_price || CHR(10) ||
        'Subtotal: ' || in_subtotal || CHR(10) ||
        'Notes: ' || in_notes
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
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ORDER_DETAIL_INSERT';

CREATE OR REPLACE PROCEDURE sp_indra_order_detail_update
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_order_id             IN indra_order_detail.order_id%TYPE,
    in_contact_id           IN indra_order_detail.create_by%TYPE,
    in_owner_id             IN indra_order_detail.owner_id%TYPE
) IS
    v_count_exists          NUMBER;
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

    UPDATE indra_order_detail
    SET
        status = 0,
        update_by = in_contact_id,
        update_time = SYSDATE
    WHERE
        order_id = in_order_id
        AND owner_id = in_owner_id
        AND status = 1;
END;
/
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ORDER_UPDATE';

CREATE OR REPLACE PROCEDURE sp_indra_order_detail_delete
(
    out_num                 OUT NUMBER,
    out_str                 OUT VARCHAR2,
    in_order_id             IN indra_order_detail.order_id%TYPE,
    in_contact_id           IN indra_item.create_by%TYPE,
    in_owner_id             IN indra_item.owner_id%TYPE
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
        out_str := 'Order does not exist';

        RETURN;
    END IF;

    DELETE FROM indra_order_detail
    WHERE
        order_id = in_order_id;
END;
/
SELECT * FROM USER_ERRORS WHERE NAME = 'SP_INDRA_ORDER_DELETE';

SELECT text FROM user_source WHERE name = 'SP_NAME' ORDER BY line;