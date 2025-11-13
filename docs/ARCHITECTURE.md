# Arquitetura do Projeto

## Estrutura do Projeto

```
.
├── backend/                    # Aplicação Laravel (API)
│   ├── app/
│   │   ├── Exceptions/        # Exceções customizadas
│   │   ├── Http/
│   │   │   ├── Controllers/   # Controllers da API
│   │   │   ├── Requests/      # Form Requests (validação)
│   │   │   └── Resources/     # API Resources (transformação)
│   │   ├── Models/            # Models Eloquent
│   │   └── Services/          # Lógica de negócio
│   ├── database/
│   │   ├── migrations/        # Migrations do banco
│   │   └── seeders/           # Seeders de dados
│   ├── routes/
│   │   └── api.php           # Rotas da API
│   └── tests/                # Testes automatizados
│       ├── Feature/          # Testes de integração
│       └── Unit/             # Testes unitários
│
├── frontend/                  # Aplicação Vue.js (SPA)
│   ├── src/
│   │   ├── components/       # Componentes reutilizáveis
│   │   ├── composables/      # Composables Vue
│   │   ├── router/           # Configuração de rotas
│   │   ├── views/            # Páginas da aplicação
│   │   └── types/            # Tipos TypeScript
│   └── public/               # Assets estáticos
│
├── docker-compose.yml        # Orquestração dos containers
├── .env                      # Variáveis de ambiente
└── README.md                 # Este arquivo
```

## Arquitetura Backend

O backend segue uma arquitetura em camadas:

- **Controllers:** Recebem requisições e retornam respostas
- **Form Requests:** Validam dados de entrada
- **Services:** Contêm lógica de negócio
- **Models:** Representam entidades do banco de dados
- **Resources:** Transformam dados para resposta da API
- **Exceptions:** Tratam erros de negócio

## Arquitetura Frontend

O frontend utiliza composables para lógica reutilizável:

- **Views:** Páginas da aplicação
- **Components:** Componentes UI reutilizáveis
- **Composables:** Lógica compartilhada (auth, API, notificações)
- **Router:** Gerenciamento de rotas e guards
