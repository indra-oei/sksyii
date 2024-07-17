-- User Table
DROP SEQUENCE seq_indra_user_id;
DROP TABLE indra_user;

CREATE SEQUENCE seq_indra_user_id
    START WITH 1
    INCREMENT BY 1;

CREATE TABLE indra_user (
    id              NUMBER,
    name            VARCHAR2(128),
    email           VARCHAR2(256),
    password        VARCHAR2(64),
    create_by       NUMBER,
    create_time     DATE DEFAULT SYSDATE,
    update_by       NUMBER,
    update_time     DATE,
    owner_id        NUMBER,
    status          NUMBER DEFAULT 1
);

-- Category Table
DROP SEQUENCE seq_indra_category_id;
DROP TABLE indra_category;

CREATE SEQUENCE seq_indra_category_id 
    START WITH 1 
    INCREMENT BY 1;

CREATE TABLE indra_category 
(
    id              NUMBER PRIMARY KEY,
    name            VARCHAR2(128),
    create_by       NUMBER,
    create_time     DATE DEFAULT SYSDATE,
    update_by       NUMBER,
    update_time     DATE,
    status          NUMBER DEFAULT 1,
    owner_id        NUMBER
);

-- Sub Category Table
DROP SEQUENCE seq_indra_subcategory_id;
DROP TABLE indra_subcategory;

CREATE SEQUENCE seq_indra_subcategory_id
    START WITH 1 
    INCREMENT BY 1;

CREATE TABLE indra_subcategory
(
    id              NUMBER PRIMARY KEY,
    category_id     NUMBER,
    name            VARCHAR2(128), 
    create_by       NUMBER,
    create_time     DATE DEFAULT SYSDATE,
    update_by       NUMBER,
    update_time     DATE,
    status          NUMBER DEFAULT 1,
    owner_id        NUMBER
);

-- Item Table
DROP SEQUENCE seq_indra_item_id;
DROP TABLE indra_item;

CREATE SEQUENCE seq_indra_item_id
    START WITH 1 
    INCREMENT BY 1;

CREATE TABLE indra_item
(
    id              NUMBER PRIMARY KEY,
    subcategory_id  NUMBER,
    name            VARCHAR2(128),
    description     VARCHAR2(256),
    price           NUMBER,
    valid_until     DATE,
    create_by       NUMBER,
    create_time     DATE DEFAULT SYSDATE,
    update_by       NUMBER,
    update_time     DATE,
    status          NUMBER DEFAULT 1,
    owner_id        NUMBER
);

CREATE TABLE indra_global_variable
(
    var_name        VARCHAR2(64),
    var_value       VARCHAR2(64)
);

DROP SEQUENCE seq_indra_order_id;
DROP TABLE indra_order;

CREATE SEQUENCE seq_indra_order_id
    START WITH 1
    INCREMENT BY 1;

CREATE TABLE indra_order
(
    id                  NUMBER PRIMARY KEY,
    order_no            VARCHAR2(6),
    customer_name       VARCHAR2(128),
    customer_sex        CHAR(1),
    table_no            NUMBER,
    total_customer      NUMBER,
    transaction_date    DATE DEFAULT SYSDATE,
    order_status        VARCHAR2(32) DEFAULT 'New',
    create_by           NUMBER,
    create_time         DATE DEFAULT SYSDATE,
    update_by           NUMBER,
    update_time         DATE,
    status              NUMBER DEFAULT 1,
    owner_id            NUMBER
);

DROP SEQUENCE seq_indra_order_detail_id;
DROP TABLE indra_order_detail;

CREATE SEQUENCE seq_indra_order_detail_id
    START WITH 1
    INCREMENT BY 1;

CREATE TABLE indra_order_detail
(
    id                  NUMBER PRIMARY KEY,
    order_id            NUMBER,
    item_name           VARCHAR2(128),
    item_quantity       NUMBER,
    item_price          NUMBER,
    subtotal            NUMBER,
    notes               VARCHAR2(4000),
    create_by           NUMBER,
    create_time         DATE DEFAULT SYSDATE,
    update_by           NUMBER,
    update_time         DATE,
    status              NUMBER DEFAULT 1,
    owner_id            NUMBER
);

DROP SEQUENCE seq_indra_transaction_id;
DROP TABLE indra_transaction;

CREATE SEQUENCE seq_indra_transaction_id
    START WITH 1
    INCREMENT BY 1;

CREATE TABLE indra_transaction
(
    id                          NUMBER PRIMARY KEY,
    order_id                    NUMBER,
    total                       NUMBER,
    service_amount              NUMBER,
    tax_amount                  NUMBER,
    grand_total                 NUMBER,
    payment_date                DATE,
    create_by                   NUMBER,
    create_time                 DATE DEFAULT SYSDATE,
    update_by                   NUMBER,
    update_time                 DATE,
    status                      NUMBER DEFAULT 1,
    owner_id                    NUMBER
);

UPDATE indra_transaction
SET
    payment_date = SYSDATE,
    status = 2,
    update_by = 1,
    update_time = SYSDATE
WHERE
    order_id = 13
    AND owner_id = 1
    AND status = 1;

SELECT
    *
FROM
    indra_order_detail iod,
    indra_order io
WHERE
    iod.order_id = io.id
    AND io.order_no = '7050AY';


-- History Table
DROP SEQUENCE seq_indra_history_id;
DROP TABLE indra_history;

CREATE SEQUENCE seq_indra_history_id
    START WITH 1
    INCREMENT BY 1;

CREATE TABLE indra_history
(
    id              NUMBER PRIMARY KEY,
    action_type     VARCHAR(128),
    action_desc     CLOB,
    create_by       NUMBER,
    create_time     DATE DEFAULT SYSDATE,
    owner_id        NUMBER
);