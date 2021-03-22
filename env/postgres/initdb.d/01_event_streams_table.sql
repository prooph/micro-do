CREATE TABLE event_streams (
  no BIGSERIAL,
  real_stream_name VARCHAR(150) NOT NULL,
  category VARCHAR(150),
  stream_name CHAR(41) NOT NULL,
  metadata JSONB,
  PRIMARY KEY (no),
  UNIQUE (stream_name)
);
