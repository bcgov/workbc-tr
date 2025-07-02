resource "aws_efs_file_system" "workbc-tr" {
  creation_token                  = "workbc-tr-efs"
  encrypted                       = true

  tags = merge(
    {
      Name = "workbc-tr-efs"
    },
    var.common_tags
  )
}

resource "aws_efs_mount_target" "data_azA" {
  file_system_id  = aws_efs_file_system.workbc-tr.id
  subnet_id       = sort(module.network.aws_subnet_ids.data.ids)[0]
  security_groups = [data.aws_security_group.app.id, aws_security_group.allow_nfs.id]
  depends_on = [aws_security_group.allow_nfs]
}

resource "aws_efs_mount_target" "data_azB" {
  file_system_id  = aws_efs_file_system.workbc-tr.id
  subnet_id       = sort(module.network.aws_subnet_ids.data.ids)[1]
  security_groups = [data.aws_security_group.app.id, aws_security_group.allow_nfs.id]
  depends_on = [aws_security_group.allow_nfs]
}
  
resource "aws_efs_backup_policy" "workbc-tr-efs-backups-policy" {
  file_system_id = aws_efs_file_system.workbc-tr.id

  backup_policy {
    status = "ENABLED"
  }
}
