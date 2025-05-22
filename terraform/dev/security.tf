# security.tf

data "aws_security_group" "web" {
  name = "Web_sg"
}

data "aws_security_group" "app" {
  name = "App_sg"
}

data "aws_security_group" "data" {
  name = "Data_sg"
}

# Traffic to the ECS cluster should only come from the ALB
/*
data "aws_security_group" "ecs_tasks" {
  name        = "workbc-cc-ecs-tasks-security-group"
}


data "aws_security_group" "efs_security_group" {
  name        = "workbc-cc-efs-security-group"
}*/

resource "aws_security_group" "allow_nfs" {
  name        = "allow_nfs"
  description = "Allow NFS inbound traffic and all outbound traffic"
  vpc_id      = module.network.aws_vpc.id

  tags = {
    Name = "allow_nfs"
  }
}

resource "aws_vpc_security_group_ingress_rule" "allow_nfs_ipv4" {
  security_group_id = aws_security_group.allow_nfs.id
  cidr_ipv4         = module.network.aws_vpc.cidr_block
  from_port         = 2049
  ip_protocol       = "tcp"
  to_port           = 2049
}

resource "aws_vpc_security_group_egress_rule" "allow_all_traffic_ipv4" {
  security_group_id = aws_security_group.allow_nfs.id
  cidr_ipv4         = "0.0.0.0/0"
  ip_protocol       = "-1" # semantically equivalent to all ports
}

resource "aws_security_group" "allow_postgres" {
  name        = "allow_postgres"
  description = "Allow Postgres inbound traffic and all outbound traffic"
  vpc_id      = module.network.aws_vpc.id

  tags = {
    Name = "allow_postgres"
  }
}

resource "aws_vpc_security_group_ingress_rule" "allow_postgres_ipv4" {
  security_group_id = aws_security_group.allow_postgres.id
  cidr_ipv4         = module.network.aws_vpc.cidr_block
  from_port         = 5432
  ip_protocol       = "tcp"
  to_port           = 5432
}

resource "aws_vpc_security_group_egress_rule" "allow_all_traffic_ipv4a" {
  security_group_id = aws_security_group.allow_postgres.id
  cidr_ipv4         = "0.0.0.0/0"
  ip_protocol       = "-1" # semantically equivalent to all ports
}
