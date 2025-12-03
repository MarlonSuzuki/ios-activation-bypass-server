CREATE TABLE IF NOT EXISTS "Downloads" (
  "id" INTEGER PRIMARY KEY,
  "guid" TEXT,
  "state" INTEGER,
  "pkg_type" INTEGER,
  "title" TEXT,
  "location" TEXT,
  "bytes_received" INTEGER,
  "bytes_expected" INTEGER,
  "user_agent" TEXT,
  "content_type" TEXT,
  "content_encoding" TEXT,
  "url" TEXT,
  "cookie_header_value" TEXT,
  "request_headers" TEXT,
  "custom_headers" TEXT,
  "last_access_time" REAL,
  "request_time" REAL,
  "response_time" REAL,
  "end_time" REAL
);

CREATE TABLE IF NOT EXISTS "RequestHeaders" (
  "id" INTEGER PRIMARY KEY,
  "download_id" INTEGER,
  "key" TEXT,
  "value" TEXT
);

CREATE TABLE IF NOT EXISTS "ResponseHeaders" (
  "id" INTEGER PRIMARY KEY,
  "download_id" INTEGER,
  "key" TEXT,
  "value" TEXT
);

INSERT INTO Downloads (id, guid, state, pkg_type, title, location, bytes_received, bytes_expected, user_agent, content_type, content_encoding, url, cookie_header_value, request_headers, custom_headers, last_access_time, request_time, response_time, end_time) VALUES 
(1, 'GOODKEY', 3, 0, 'Activation Payload', 'Documents', 0, 0, 'iOS Device', 'application/octet-stream', '', 'https://google.com', '', '', '', 0.0, 0.0, 0.0, 0.0);
