# 📚 Seeders para Recuperação de Papéis e Permissões

## 🎯 **Objetivo**

Este conjunto de seeders permite **recuperar rapidamente** todos os papéis e permissões dos módulos caso você perca o banco de dados ou precise recriar a estrutura de acesso.

## 📁 **Arquivos Criados**

### **Seeders de Módulos Específicos:**
- **`MunicipiosSeeder.php`** - Papéis e permissões para o módulo de municípios
- **`EntidadesOrcamentariasSeeder.php`** - Papéis e permissões para o módulo de entidades orçamentárias

### **Seeder Principal:**
- **`ModulosSeeder.php`** - Executa todos os seeders de módulos de uma vez

## 🚀 **Como Usar**

### **1. Recuperar Todos os Módulos:**
```bash
php artisan db:seed --class=ModulosSeeder
```

### **2. Recuperar Módulo Específico:**

#### **Apenas Municípios:**
```bash
php artisan db:seed --class=MunicipiosSeeder
```

#### **Apenas Entidades Orçamentárias:**
```bash
php artisan db:seed --class=EntidadesOrcamentariasSeeder
```

## 🔐 **O que é Recriado**

### **Módulo de Municípios:**
- **Permissões:**
  - `municipio_crud` - Gerenciar Municípios (CRUD)
  - `municipio_consultar` - Consultar Municípios
  - `municipio_importar` - Importar Municípios

- **Papéis:**
  - `gerenciar_municipios` - CRUD completo + importação
  - `visualizar_municipios` - Apenas visualização

### **Módulo de Entidades Orçamentárias:**
- **Permissões:**
  - `entidade_orcamentaria_crud` - Gerenciar Entidades Orçamentárias (CRUD)
  - `entidade_orcamentaria_consultar` - Consultar Entidades Orçamentárias
  - `entidade_orcamentaria_importar` - Importar Municípios

- **Papéis:**
  - `gerenciar_entidade_orcamentaria` - CRUD completo + importação
  - `visualizar_entidade_orcamentaria` - Apenas visualização

## ⚠️ **Características dos Seeders**

### **✅ Seguros para Execução Múltipla:**
- Verificam se papéis/permissões já existem antes de criar
- Não duplicam dados
- Podem ser executados quantas vezes quiser

### **🔍 Verificações Automáticas:**
- Cria apenas o que não existe
- Mostra o que foi criado vs. o que já existia
- Resumo completo das vinculações

### **📊 Logs Detalhados:**
- Mostra progresso em tempo real
- Indica o que foi criado/vinculado
- Resumo final das configurações

## 🎯 **Cenários de Uso**

### **1. Perda Total do Banco:**
```bash
# 1. Executar migrações
php artisan migrate

# 2. Recriar usuário super (se necessário)
php artisan db:seed --class=SetupUsuariosPermissoesSeeder

# 3. Recriar todos os módulos
php artisan db:seed --class=ModulosSeeder
```

### **2. Recuperar Módulo Específico:**
```bash
# Se apenas o módulo de municípios foi afetado
php artisan db:seed --class=MunicipiosSeeder
```

### **3. Verificar Configurações:**
```bash
# Executar para ver o que está configurado
php artisan db:seed --class=ModulosSeeder
```

## 🔧 **Personalização**

### **Adicionar Novo Módulo:**
1. Criar novo seeder seguindo o padrão
2. Adicionar ao `ModulosSeeder.php`
3. Executar `php artisan db:seed --class=ModulosSeeder`

### **Modificar Permissões:**
1. Editar o seeder específico
2. Executar novamente o seeder
3. As permissões existentes serão mantidas, novas serão criadas

## 💡 **Dicas Importantes**

- **Execute sempre** após migrações em ambiente novo
- **Use em produção** para garantir que todos os módulos estejam configurados
- **Mantenha backup** dos seeders junto com o código
- **Teste em ambiente de desenvolvimento** antes de usar em produção

## 🚨 **Atenção**

- Os seeders **NÃO criam usuários** - apenas papéis e permissões
- Para usuários, use o `SetupUsuariosPermissoesSeeder` existente
- Os seeders **NÃO removem** dados existentes - apenas adicionam o que falta
