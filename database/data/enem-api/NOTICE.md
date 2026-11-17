# Aviso de origem e modificações

Esta pasta contém uma cópia modificada do conjunto de dados do projeto
[enem.dev](https://github.com/yunger7/enem-api) (pasta `public`), licenciado sob a
GNU General Public License, versão 2 (texto completo em `LICENSE`).

- Origem: https://github.com/yunger7/enem-api, commit `938c56fbd3b391e03d93316c920a90c3e54842e5`.
- Os arquivos foram modificados em relação ao original (GPL-2.0, seção 2a). As
  modificações estão registradas no histórico do Git deste repositório, a partir
  de 02/11/2026 (`git log -- database/data/enem-api`):
  - preenchimento de enunciados ausentes;
  - substituição de imagens com baixa legibilidade;
  - inclusão de questões ausentes, inclusive anuladas (`correctAlternative: null`);
  - correção de áreas de conhecimento e da marcação de idioma.

A lista completa de arquivos alterados é obtida comparando esta pasta com o
commit de origem acima.
