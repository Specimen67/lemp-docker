CREATE TABLE IF NOT EXISTS machines (
    service_tag VARCHAR(50) PRIMARY KEY,
    lieu_geographique VARCHAR(100),
    ip_privee VARCHAR(15),
    ip_publique VARCHAR(15),
    port_public VARCHAR(10),
    image VARCHAR(50),
    reservee BOOLEAN DEFAULT 0,
    reservee_du DATE NULL,
    reservee_jusquau DATE NULL
);

GRANT ALL PRIVILEGES ON lemp_db.* TO 'lemp_user'@'%';
GRANT FILE ON *.* TO 'lemp_user'@'%';
FLUSH PRIVILEGES;