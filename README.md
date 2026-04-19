# Projet-Ansible-Reseautique4
## Deploiement automatise avec Ansible - College Boreal

### Description
Automatisation du deploiement d'une application web de reservation medicale (CliniqueBoreal) sur une VM Ubuntu hebergee sur Microsoft Azure.

### Stack technique
- Ansible Core 2.16.3
- Apache2 + MySQL + PHP
- Ubuntu 24.04 LTS
- Microsoft Azure

### Liens
- Site patient : http://4.239.243.153
- Espace medecin : http://4.239.243.153/admin.php
- Suivi reservation : http://4.239.243.153/suivi.php

### Fichiers
| Fichier | Description |
|---|---|
| deploy_apache.yml | Playbook principal Ansible |
| inventory.ini | Fichier d'inventaire |
| ansible.cfg | Configuration Ansible |
| setup_db.sql | Script SQL base de donnees |
| index.php | Site patient |
| admin.php | Espace medecin |
| suivi.php | Suivi patient |

### Execution
```bash
ansible-playbook -i inventory.ini deploy_apache.yml
```

### Cours
Reseautique 4 | College Boreal | Avril 2026
