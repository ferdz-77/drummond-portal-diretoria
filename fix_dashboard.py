import re

# Ler o arquivo
with open('js/dashboard.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Padrão para encontrar: var ctxName = document.getElementById("..."); if (ctxName) { ... ctxName = ctxName.getContext("2d");
# Substituir por: var canvasName = document.getElementById("..."); if (canvasName) { var ctxName = canvasName.getContext("2d");

# Lista de contextos que precisam ser corrigidos
contexts = [
    'ctxConsolidado',
    'ctxChamadosPorPortal',
    'ctxOuvidoriaStatus',
    'ctxOuvidoriaTipos',
    'ctxEADStatus',
    'ctxEADServicos',
    'ctxProcessoStatus',
    'ctxProcessoServicos',
    'ctxSecretariaStatus',
    'ctxSecretariaServicos',
    'ctxFinanceiroStatus',
    'ctxFinanceiroServicos',
    'ctxExAlunoStatus',
    'ctxExAlunoServicos'
]

for ctx in contexts:
    canvas = ctx.replace('ctx', 'canvas')
    
    # Substituir a declaração inicial: var ctxName = document.getElementById
    content = re.sub(
        rf'var {ctx} = document\.getElementById',
        f'var {canvas} = document.getElementById',
        content
    )
    
    # Substituir: if (ctxName) {
    content = re.sub(
        rf'if \({ctx}\) {{',
        f'if ({canvas}) {{',
        content
    )
    
    # Substituir: ctxName = ctxName.getContext("2d");
    content = re.sub(
        rf'{ctx} = {ctx}\.getContext\("2d"\);',
        f'var {ctx} = {canvas}.getContext("2d");',
        content
    )
    
    # Substituir referências ao estilo: ctxName.style.display
    content = re.sub(
        rf'{ctx}\.style\.display',
        f'{canvas}.style.display',
        content
    )
    
    # Substituir: ctxName.parentElement
    content = re.sub(
        rf'{ctx}\.parentElement',
        f'{canvas}.parentElement',
        content
    )

# Salvar o arquivo corrigido
with open('js/dashboard.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Correções aplicadas com sucesso!")
