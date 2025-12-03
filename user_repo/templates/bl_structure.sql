CREATE TABLE IF NOT EXISTS "BLVersionsOBJ" (
  "iBooksVersion" TEXT,
  "FileFormatVersion" TEXT
);

CREATE TABLE IF NOT EXISTS "BLDatabaseInfo" (
  "InfoType" TEXT NOT NULL PRIMARY KEY,
  "Value" TEXT
);

INSERT INTO BLDatabaseInfo (InfoType, Value) VALUES ('BLVersionsOBJ', '{"iBooksVersion":"1.0","FileFormatVersion":"1.0"}');

CREATE TABLE IF NOT EXISTS "BLAssetDownloads" (
  "ROWID" INTEGER PRIMARY KEY,
  "id" TEXT,
  "assetId" TEXT,
  "productId" TEXT,
  "paymentMethod" TEXT,
  "purchaseDate" TEXT,
  "expirationDate" TEXT,
  "productType" INTEGER,
  "downloadState" INTEGER,
  "downloadProgress" REAL,
  "assetURL" TEXT,
  "metadataURL" TEXT,
  "sequenceNumber" INTEGER
);

CREATE TABLE IF NOT EXISTS "BLAssetMetadata" (
  "ROWID" INTEGER PRIMARY KEY,
  "assetId" TEXT UNIQUE,
  "metadata" BLOB
);

CREATE TABLE IF NOT EXISTS "BLPurchases" (
  "ROWID" INTEGER PRIMARY KEY,
  "id" TEXT UNIQUE,
  "productId" TEXT,
  "kind" INTEGER,
  "timestamp" INTEGER,
  "externalVersionIdentifier" TEXT,
  "kind_contentType" TEXT
);

CREATE TABLE IF NOT EXISTS "BLPurchaseBundles" (
  "ROWID" INTEGER PRIMARY KEY,
  "id" TEXT UNIQUE,
  "products" TEXT,
  "timestamp" INTEGER
);
