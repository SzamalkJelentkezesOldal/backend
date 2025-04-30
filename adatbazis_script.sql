CREATE DATABASE jelentkezoOldal;
GO

USE jelentkezoOldal;
GO

CREATE TABLE allapotszotar (
    id INT IDENTITY(1,1) PRIMARY KEY,
    elnevezes NVARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);
GO

SET IDENTITY_INSERT allapotszotar ON;
INSERT INTO allapotszotar (id, elnevezes, created_at, updated_at) VALUES
(1,N'Jelentkezett','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(2,N'Regisztrált','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(3,N'Törzsadatok feltöltve','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(4,N'Dokumentumok feltöltve','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(5,N'Eldöntésre vár','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(6,N'Módosításra vár','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(7,N'Elfogadva','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(8,N'Elutasítva','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(9,N'Lezárt','2025-04-29 20:47:18','2025-04-29 20:47:18'),
(10,N'Archivált','2025-04-29 20:47:18','2025-04-29 20:47:18');
SET IDENTITY_INSERT allapotszotar OFF;
GO

CREATE TABLE dokumentum_tipus (
    id INT IDENTITY(1,1) PRIMARY KEY,
    elnevezes NVARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);
GO

SET IDENTITY_INSERT dokumentum_tipus ON;
INSERT INTO dokumentum_tipus (id, elnevezes, created_at, updated_at) VALUES
(1,N'Adóigazolvány','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(2,N'TAJ kártya','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(3,N'Személyazonosító igazolvány első oldala','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(4,N'Személyazonosító igazolvány hátsó oldala','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(5,N'Lakcímet igazoló igazolvány első oldala','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(6,N'Lakcímet igazoló igazolvány hátsó oldala','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(7,N'Érettségi bizonyítvány','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(8,N'Tanulmányi dokumentumok','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(9,N'Önarckép','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(10,N'SNI/BTMN','2025-04-29 20:47:19','2025-04-29 20:47:19'),
(11,N'Nyilatkozatok','2025-04-29 20:47:19','2025-04-29 20:47:19');
SET IDENTITY_INSERT dokumentum_tipus OFF;
GO

CREATE TABLE jelentkezo (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nev NVARCHAR(255) NOT NULL,
    email NVARCHAR(255) NOT NULL,
    tel NVARCHAR(20),
    token NVARCHAR(255),
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);
GO

SET IDENTITY_INSERT jelentkezo ON;
INSERT INTO jelentkezo (id, nev, email, tel, token, created_at, updated_at) VALUES
(1,N'Teszt Felhasználó',N'felhasznalo@felhasznalo.com',N'06202020200',N'aaaaaaaaaaaaaaaaaaaa','2025-04-29 20:47:18','2025-04-29 20:47:18');
SET IDENTITY_INSERT jelentkezo OFF;
GO

CREATE TABLE szak (
    id INT IDENTITY(1,1) PRIMARY KEY,
    elnevezes NVARCHAR(255) NOT NULL,
    portfolio BIT NOT NULL,
    nappali BIT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);
GO

SET IDENTITY_INSERT szak ON;
INSERT INTO szak (id, elnevezes, portfolio, nappali, created_at, updated_at) VALUES
(1,N'Informatikai rendszer- és alkalmazás-üzemeltető technikus',0,1,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(2,N'Szoftverfejlesztő és tesztelő',0,1,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(3,N'Dekoratőr',0,1,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(4,N'Divat-, jelmez- és díszlettervező (Divattervező)',0,1,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(5,N'Fotográfus (Kreatív fotográfus)',1,1,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(6,N'Grafikus',1,1,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(7,N'Mozgókép- és animációkészítő',1,1,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(8,N'Informatikai rendszer- és alkalmazás-üzemeltető technikus',0,0,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(9,N'Szoftverfejlesztő és tesztelő',0,0,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(10,N'Dekoratőr',0,0,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(11,N'Divat-, jelmez- és díszlettervező (Divattervező)',0,0,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(12,N'Fotográfus (Kreatív fotográfus)',1,0,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(13,N'Grafikus',1,0,'2025-04-29 20:47:18','2025-04-29 20:47:18'),
(14,N'Mozgókép- és animációkészítő',1,0,'2025-04-29 20:47:18','2025-04-29 20:47:18');
SET IDENTITY_INSERT szak OFF;
GO

CREATE TABLE jelentkezes (
    id INT IDENTITY(1,1) PRIMARY KEY,
    jelentkezo_id INT NOT NULL,
    szak_id INT NOT NULL,
    allapot INT NOT NULL,
    sorrend INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (jelentkezo_id) REFERENCES jelentkezo(id),
    FOREIGN KEY (szak_id) REFERENCES szak(id),
    FOREIGN KEY (allapot) REFERENCES allapotszotar(id)
);
GO

SET IDENTITY_INSERT jelentkezes ON;
INSERT INTO jelentkezes (id, jelentkezo_id, szak_id, allapot, sorrend, created_at, updated_at) VALUES
(1,1,1,2,0,'2025-04-29 20:47:19','2025-04-29 20:47:19'),
(2,1,9,2,0,'2025-04-29 20:47:19','2025-04-29 20:47:19');
SET IDENTITY_INSERT jelentkezes OFF;
GO

CREATE TABLE dokumentumok (
    id INT IDENTITY(1,1) PRIMARY KEY,
    jelentkezo_id INT NOT NULL,
    dokumentum_tipus_id INT NOT NULL,
    fajlok NVARCHAR(MAX) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (jelentkezo_id) REFERENCES jelentkezo(id),
    FOREIGN KEY (dokumentum_tipus_id) REFERENCES dokumentum_tipus(id)
);
GO

CREATE TABLE jelentkezo_torzs (
    jelentkezo_id INT PRIMARY KEY,
    vezeteknev NVARCHAR(255) NOT NULL,
    keresztnev NVARCHAR(255) NOT NULL,
    adoazonosito NVARCHAR(255) UNIQUE NULL,
    lakcim NVARCHAR(255) NOT NULL,
    taj_szam NVARCHAR(255) UNIQUE NULL,
    szuletesi_hely NVARCHAR(255) NOT NULL,
    szuletesi_nev NVARCHAR(255) NULL,
    szuletesi_datum DATE NOT NULL,
    allampolgarsag NVARCHAR(255) NOT NULL,
    anyja_neve NVARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (jelentkezo_id) REFERENCES jelentkezo(id)
);
GO

CREATE TABLE portfolios (
    id INT IDENTITY(1,1) PRIMARY KEY,
    jelentkezo_id INT NOT NULL,
    szak_id INT NOT NULL,
    portfolio_url NVARCHAR(255) NOT NULL,
    allapot NVARCHAR(50) NOT NULL DEFAULT N'Eldöntésre vár',
    ertesito BIT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE (jelentkezo_id, szak_id, portfolio_url),
    FOREIGN KEY (jelentkezo_id) REFERENCES jelentkezo(id),
    FOREIGN KEY (szak_id) REFERENCES szak(id)
);
GO

CREATE TABLE statuszvaltozas (
    id INT IDENTITY(1,1) PRIMARY KEY,
    jelentkezo_id INT NOT NULL,
    szak_id INT NOT NULL,
    regi_allapot INT NOT NULL,
    uj_allapot INT NOT NULL,
    modositas_ideje DATETIME NOT NULL DEFAULT GETDATE(),
    user_id INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (jelentkezo_id) REFERENCES jelentkezo(id),
    FOREIGN KEY (szak_id) REFERENCES szak(id),
    FOREIGN KEY (regi_allapot) REFERENCES allapotszotar(id),
    FOREIGN KEY (uj_allapot) REFERENCES allapotszotar(id)
);
GO

CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(255) NOT NULL,
    email NVARCHAR(255) NOT NULL,
    email_verified_at DATETIME NULL,
    password NVARCHAR(255) NOT NULL,
    role INT NOT NULL,
    remember_token NVARCHAR(100) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);
GO

SET IDENTITY_INSERT users ON;
INSERT INTO users (id,name,email,email_verified_at,password,role,remember_token,created_at,updated_at) VALUES
(1,N'Ügyintéző',N'admin@admin.com',NULL,N'$2y$04$8XaO1IZqyy8VbdBj2ct9aO80c.KJgjkusr7teRbfpPtC5rU.RJ/kO',1,NULL,'2025-04-29 20:47:19','2025-04-29 20:47:19'),
(2,N'Master',N'master@master.com',NULL,N'$2y$04$jcRQmpSoXktVK2tQxrtEj.sZClNqOCkYxu4WMEGm8kefzf.sMF3pO',2,NULL,'2025-04-29 20:47:19','2025-04-29 20:47:19'),
(3,N'Felhasználó',N'felhasznalo@felhasznalo.com',NULL,N'$2y$04$mtocctWfE1zUPGB8t8PpueMjh5rqH93xu0uqFSyhs30sUjoT8t1ty',0,NULL,'2025-04-29 20:47:19','2025-04-29 20:47:19');
SET IDENTITY_INSERT users OFF;
GO
