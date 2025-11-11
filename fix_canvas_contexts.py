import re

# Ler o arquivo
with open('js/dashboard.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Padrão para encontrar: var ctxNome = document.getElementById...  ctxNome = ctxNome.getContext
# E substituir por: var canvasNome = document.getElementById...  var ctxNome = canvasNome.getContext

patterns = [
    ('ctxEADStatus', 'chartEADStatus'),
    ('ctxEADServicos', 'chartEADServicos'),
    ('ctxProcessoStatus', 'chartProcessoStatus'),
    ('ctxProcessoServicos', 'chartProcessoServicos'),
    ('ctxSecretariaStatus', 'chartSecretariaStatus'),
    ('ctxSecretariaServicos', 'chartSecretariaServicos'),
    ('ctxFinanceiroStatus', 'chartFinanceiroStatus'),
    ('ctxFinanceiroServicos', 'chartFinanceiroServicos'),
    ('ctxExAlunoStatus', 'chartExAlunoStatus'),
    ('ctxExAlunoServicos', 'chartExAlunoServicos'),
]

for ctx_name, canvas_id in patterns:
    canvas_var = ctx_name.replace('ctx', 'canvas')
    
    # Substituir a declaração inicial
    pattern1 = f'var {ctx_name} = document.getElementById("{canvas_id}");'
    replacement1 = f'var {canvas_var} = document.getElementById("{canvas_id}");'
    content = content.replace(pattern1, replacement1)
    
    # Substituir o if
    pattern2 = f'if ({ctx_name}) {{'
    replacement2 = f'if ({canvas_var}) {{'
    content = content.replace(pattern2, replacement2)
    
    # Substituir a atribuição do contexto
    pattern3 = f'{ctx_name} = {ctx_name}.getContext("2d");'
    replacement3 = f'var {ctx_name} = {canvas_var}.getContext("2d");'
    content = content.replace(pattern3, replacement3)
    
    # Substituir o else com .style.display
    pattern4 = f'{ctx_name}.style.display = \'none\';\n    const container = {ctx_name}.parentElement;\n    const noDataMsg = document.createElement(\'p\');\n    noDataMsg.className = \'no-data\';\n    noDataMsg.textContent = \'Nenhum dado encontrado para o período selecionado.\';\n    container.appendChild(noDataMsg);'
    replacement4 = f'hideCanvasAndShowNoData("{canvas_id}");'
    content = content.replace(pattern4, replacement4)

# Salvar o arquivo
with open('js/dashboard.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Arquivo corrigido com sucesso!")
