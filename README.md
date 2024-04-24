<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# comands

```sh
./vendor/bin/sail artisan --version
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan migrate:refresh
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan livewire:layout
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm install && npm run dev 
```

# MailHog docker-compose env 
- with local storage
- web ui on port 8025 with login "test"  and password "test" 
- smtp on port 2025 with login="" and password=""

## Change login and password

 `printf "login:" > mailhog.auth`
 
 `printf "$(sudo docker exec  mailhog /bin/sh -c "MailHog bcrypt password")" >> mailhog.auth`
  - mailhog - name of docker container mailhog

 `cat mailhog.auth`

## About
###  GoHighLevel: A Ferramenta Mais Disruptiva do Mercado Digital 

 Introdução:

No ecossistema digital em constante evolução, agências de marketing, infoprodutores, negócios locais e empreendores digitais buscam soluções que não apenas simplifiquem suas operações, mas também ofereçam valor agregado aos seus clientes e negócios.

O GoHighLevel surge como uma resposta a essa demanda, prometendo ser uma solução completa para as necessidades de marketing digital que abrange pequenos negócios locais (embora não recomendo a utilização e você vai saber porque até o fim deste blog), agências de marketing digital e criadores de infoprodutos.

Esta análise detalhada será um conteúdo que vou estar sempre atualizando para explorar e destrinchar todos os recursos desta ferramenta. Garanto que mesmo que você já use a ferramenta há algum tempo, você vai acabar descobrindo coisas novas.

Baseada em experiência de uso de mais de dois anos aplicando para clientes e para meus próprios projetos digitais.

Para assinar o GoHighLevel, clique
- https://www.gohighlevel.com/?fp_ref=dantasghl

 O que torna o GoHighLevel Único?

O GoHighLevel não é apenas mais uma ferramenta de marketing digital no mercado. Ele se destaca por integrar uma variedade de funcionalidades - desde CRM a automação de emails, SMS e até whatsapp, passando por construtores de websites e funis, e com uma capacidae robusta de análise de dados - tudo em uma única plataforma. 

##  Contexto de Mercado 
 No cenário atual, plataformas como Pipedrive, Hubspot, e ClickFunnels dominam segmentos específicos do marketing digital. No entanto, a fragmentação das ferramentas obriga as agências a investir em múltiplas soluções, complicando a gestão e aumentando os custos. O GoHighLevel se apresenta como uma alternativa que consolida essas diversas funcionalidades, oferecendo uma solução mais coesa e integrada.

No ano de 2023, o GoHighLevel ultrapassou o ClickFunnels em número de pesquisas nos Estados Unidos e continua crescendo em popularidade no mercado. 

##  As Funcionalidades do GoHighLevel 

Entender todas as funcionalidades do GoHighLevel é essencial, tanto para donos de agências quanto para vendedores de info produtos que estão pensando em usar essa ferramenta. Aqui vou listar cada um dos recursos incluindo screenshots para que você saiba visualmente como a ferramenta apresenta.

##  Gerenciamento Avançado de Contatos 

No GoHighLevel, o gerenciamento de contatos vai além da simples coleta de informações. Ele permite a criação de listas inteligentes, adição de tags, classificação por empresa, e realização de ações em massa. A visão individual de cada contato é detalhada, oferecendo um panorama completo de suas interações, histórico e dados relevantes.

##  CRM e Gestão de Pipelines 

 A espinha dorsal do GoHighLevel é o seu CRM (Customer Relationship Management) e a gestão de pipelines. Estes recursos permitem aos usuários uma visão clara e organizada dos clientes, leads e suas respectivas jornadas de compra. O CRM é intuitivo e fácil de usar, tornando o acompanhamento de interações e progresso de vendas uma tarefa simplificada.

O CRM e as pipelines funcionam como o esqueleto do GoHighLevel.

O GoHighLevel oferece um CRM simples de ser usado e é visualmente intuitivo a ponto de facilitar bastante o uso. Ele permite uma visão de todos os seus leads e clientes e seus respectivos estágios de venda e contratos. 

## Onboarding de Clientes

A primeira impressão é crucial, e a plataforma de lançamento do GoHighLevel garante que seja impactante. Personalizável e intuitiva, ela orienta os usuários na integração com as principais ferramentas e recursos, como aplicativos móveis, Google Business, Facebook, Stripe, WordPress, entre outros.

## Painel de Relatórios

O painel de relatórios do GoHighLevel é uma obra-prima de design e funcionalidade. Ele oferece uma visão abrangente de métricas chave, como status e valor das oportunidades, taxa de conversão, distribuição de funil, entre outras. Além disso, integra-se com ferramentas externas como Google Analytics, proporcionando uma análise de dados ainda mais profunda.

## Painel de Conversas

A comunicação eficiente com leads e clientes é vital, e o painel de conversas do GoHighLevel facilita isso magistralmente. Ele permite gerenciar comunicações multicanais, incluindo Facebook, Instagram, Email, SMS, e em breve WhatsApp, em um único lugar. Isso não apenas economiza tempo, mas também oferece insights valiosos sobre as interações com os clientes.

## Calendários e Agendamentos

O módulo de calendário do GoHighLevel é surpreendentemente versátil, suportando diferentes tipos de reservas e personalização completa. Isso inclui a adição de logotipos, descrições, configuração de disponibilidade, e integração com ferramentas externas para eventos.

## Pagamentos e Invoices

O módulo de pagamentos é uma das é o recurso para envio de propostas, estimativas, e faturas, e suporta contratos de SAAS com assinatura. A plataforma oferece integrações nativas com Stripe, mas você também pode usar outros processadores como PayPal, NMI, QuickBooks e Authorize.net, tornando os processos financeiros mais eficientes e menos propensos a erros.

## Lead Scoring Automatizado

A pontuação automática de leads é uma inovação recente do GoHighLevel, permitindo às agências focar nos leads mais promissores. Este sistema atribui pontos baseados em interações específicas, como abrir um email, facilitando a identificação dos leads mais engajados e potencialmente lucrativos.

## Marketing Integrado

A seção de marketing do GoHighLevel é uma central de ferramentas poderosas. Inclui um planejador social para gerenciar postagens em plataformas como Google Business, Facebook, Instagram, LinkedIn, Twitter e TikTok, e um robusto construtor/remetente de e-mails. Esta integração de ferramentas de marketing economiza tempo e melhora a eficácia das campanhas.

## Automações de Email e SMS (Workflow Builder)

O GoHighLevel leva a automação a um novo patamar com seu Workflow Builder. Este construtor permite criar fluxos de trabalho automatizados personalizados, com uma vasta gama de gatilhos e ações, tornando as operações de marketing mais eficientes e personalizadas.

## Construção de Websites e Funnels

construtor de websites e funnels do GoHighLevel é outra área onde a plataforma brilha. Ele oferece uma gama de funcionalidades e opções de personalização, permitindo às agências criar sites e funnels atraentes e eficazes para seus clientes.

## Prós e Contras do GoHighLevel
## Prós

    Teste gratuito de 14 dias com todos os recursos.
    Solução all-in-one economizando em múltiplas ferramentas como construtores de site, hospedagem de cursos, venda de info produtos.
    Integrações perfeitas de sistemas de e-mail, SMS e ferramentas de automação.
    Ideal para agências que queiram personalizar e comercializar diferentes aplicações para nichos diferentes.

## Contras

    Limitação de recursos gráficos nos construtores de páginas e funis.
    Curva de aprendizagem pode ser grande para novos usuários. Não esqueça de se cadastrar para obter meu curso gratuito que será lançado em 2024.
    Devido o preço ser em dólar, pode ser um pouco alto para entrada em mercados que não são de ticket alto.

## Preços GoHighLevel

Os planos de preços do GoHighLevel variam de $97 a $497 por mês, atendendo a diferentes necessidades e tamanhos de empresas. A plataforma é conhecida por sua acessibilidade e funcionalidade, oferecendo um excelente custo-benefício comparado a outras ferramentas de marketing.

## Público-Alvo

GoHighLevel é projetado principalmente para agências de marketing, mas também é ideal para profissionais de vendas, equipes de vendas, empresas locais e indivíduos que procuram soluções integradas de vendas e marketing.

## Alternativas GoHighLevel

Embora GoHighLevel seja uma plataforma robusta, existem alternativas como Vendasta, Cartra, HubSpot, Builderall, Zoho e Agile CRM que podem ser mais adequadas para certas necessidades e orçamentos.

## Conclusão

GoHighLevel é uma plataforma revolucionária de marketing digital, oferecendo uma solução abrangente para agências e profissionais de marketing. Com uma vasta gama de funcionalidades, integrações e uma abordagem centrada no usuário, é uma ferramenta valiosa para otimizar operações de marketing e vendas. Para aqueles que consideram uma solução integrada, o GoHighLevel é definitivamente uma opção a ser explorada. 

- Fonte: https://www.linkedin.com/pulse/gohighlevel-ferramenta-mais-disruptiva-do-mercado-digital-dantas-e7jjc/
