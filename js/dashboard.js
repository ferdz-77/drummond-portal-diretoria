// dashboard.js

// Função helper para esconder canvas e mostrar mensagem
function hideCanvasAndShowNoData(canvasId, message = 'Nenhum dado encontrado.') {
  var canvas = document.getElementById(canvasId);
  if (canvas) {
    canvas.style.display = 'none';
    const container = canvas.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = message;
    container.appendChild(noDataMsg);
  }
}

// Configuração padrão para gráficos pizza
function getPieChartOptions(title = '') {
  return {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 1,
    plugins: {
      legend: {
        display: true,
        position: 'bottom',
        labels: {
          boxWidth: 12,
          padding: 15,
          font: {
            size: 11
          },
          generateLabels: function(chart) {
            const data = chart.data;
            if (data.labels.length && data.datasets.length) {
              return data.labels.map((label, i) => {
                const dataset = data.datasets[0];
                const backgroundColor = dataset.backgroundColor[i];
                const value = dataset.data[i];
                const total = dataset.data.reduce((a, b) => a + b, 0);
                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0';
                
                return {
                  text: `${label}: ${value} (${percentage}%)`,
                  fillStyle: backgroundColor,
                  hidden: false,
                  index: i
                };
              });
            }
            return [];
          }
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            const label = context.label || '';
            const value = context.parsed;
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0';
            return `${label}: ${value} (${percentage}%)`;
          }
        }
      }
    },
    layout: {
      padding: {
        top: 10,
        bottom: 10,
        left: 10,
        right: 10
      }
    }
  };
}

// Cores padrão para gráficos pizza
function getPieChartColors() {
  return [
    'rgba(255, 99, 132, 0.8)',
    'rgba(54, 162, 235, 0.8)',
    'rgba(255, 206, 86, 0.8)',
    'rgba(75, 192, 192, 0.8)',
    'rgba(153, 102, 255, 0.8)',
    'rgba(255, 159, 64, 0.8)',
    'rgba(201, 203, 207, 0.8)',
    'rgba(255, 99, 255, 0.8)'
  ];
}

// Cores específicas para status dos chamados
function getStatusColors(labels) {
  const statusColorMap = {
    'Total': '#0dcaf0',
    'Em Análise': '#dc3545',
    'Respondido': '#626a72',
    'Respondido Drummond': '#F25C05',
    'Finalizado': '#0d6efd',
    'Transferido': '#6f42c1'
  };

  return labels.map(label => {
    // Procurar correspondência exata primeiro
    if (statusColorMap[label]) {
      return statusColorMap[label];
    }

    // Procurar correspondência parcial (case insensitive)
    const lowerLabel = label.toLowerCase();
    for (const [status, color] of Object.entries(statusColorMap)) {
      if (lowerLabel.includes(status.toLowerCase()) || status.toLowerCase().includes(lowerLabel)) {
        return color;
      }
    }

    // Cor padrão se não encontrar correspondência
    return '#6c757d';
  });
}

// Gráfico Consolidado - Comparação de SLA
var ctxConsolidado = document.getElementById("chartConsolidado");
if (ctxConsolidado) {
  ctxConsolidado = ctxConsolidado.getContext("2d");

  // Valores de SLA (usar valores reais, sem padrões)
  var slaData = [
    { portal: "Ouvidoria", sla: ouvidoriaSLA },
    { portal: "EAD", sla: eadSLA },
    { portal: "Processo Seletivo", sla: processoSLA },
    { portal: "Secretaria", sla: secretariaTempo },
    { portal: "Financeiro", sla: financeiroSLA },
    { portal: "Ex-Aluno", sla: exalunoSLA },
  ];

  // Ordenar por SLA do menor para o maior
  slaData.sort((a, b) => a.sla - b.sla);

  // Extrair labels e valores ordenados
  var labels = slaData.map((item) => item.portal);
  var slaValues = slaData.map((item) => item.sla);

  window.chartConsolidado = new Chart(ctxConsolidado, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "SLA Médio (dias)",
          data: slaValues,
          backgroundColor: "#001830",
          borderColor: "#ff5b00",
          borderWidth: 2,
        },
      ],
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
}

// Gráfico Total de Chamados Consolidado - Distribuição por Status
var canvasTotalChamados = document.getElementById("chartTotalChamados");
if (canvasTotalChamados) {
  if (statusConsolidado.length > 0) {
    var ctxTotalChamados = canvasTotalChamados.getContext("2d");
    var labelsTotal = statusConsolidado.map((item) => item.status);
    var dataTotal = statusConsolidado.map((item) => item.count);
    
    window.chartTotalChamados = new Chart(ctxTotalChamados, {
      type: "pie",
      data: {
        labels: labelsTotal,
        datasets: [
          {
            data: dataTotal,
            backgroundColor: getStatusColors(labelsTotal),
            borderWidth: 2,
            borderColor: '#ffffff',
          },
        ],
      },
      options: getPieChartOptions('Distribuição por Status'),
    });
  } else {
    hideCanvasAndShowNoData("chartTotalChamados");
  }
}

// Gráfico de Barras - Chamados por Portal
var ctxChamadosPorPortal = document.getElementById("chartChamadosPorPortal");
if (ctxChamadosPorPortal) {
  ctxChamadosPorPortal = ctxChamadosPorPortal.getContext("2d");
  
  // Usar dados reais se disponíveis, senão usar dados padrão
  var chartData = {
    labels: ["Ouvidoria", "EAD", "Processo Seletivo", "Secretaria", "Financeiro", "Ex-Aluno"],
    totais: [0, 0, 0, 0, 0, 0], // Valores padrão para totais
    finalizados: [0, 0, 0, 0, 0, 0], // Valores padrão para finalizados
    abertos: [0, 0, 0, 0, 0, 0] // Valores padrão para abertos
  };
  
  if (typeof totaisPorPortal !== 'undefined' && totaisPorPortal) {
    chartData.totais = [
      totaisPorPortal['Ouvidoria'] || 0,
      totaisPorPortal['EAD'] || 0,
      totaisPorPortal['Processo Seletivo'] || 0,
      totaisPorPortal['Secretaria'] || 0,
      totaisPorPortal['Financeiro'] || 0,
      totaisPorPortal['Ex-Aluno'] || 0
    ];
  }
  
  if (typeof finalizadosPorPortal !== 'undefined' && finalizadosPorPortal) {
    chartData.finalizados = [
      finalizadosPorPortal['Ouvidoria'] || 0,
      finalizadosPorPortal['EAD'] || 0,
      finalizadosPorPortal['Processo Seletivo'] || 0,
      finalizadosPorPortal['Secretaria'] || 0,
      finalizadosPorPortal['Financeiro'] || 0,
      finalizadosPorPortal['Ex-Aluno'] || 0
    ];
  }

  if (typeof abertosPorPortal !== 'undefined' && abertosPorPortal) {
    chartData.abertos = [
      abertosPorPortal['Ouvidoria'] || 0,
      abertosPorPortal['EAD'] || 0,
      abertosPorPortal['Processo Seletivo'] || 0,
      abertosPorPortal['Secretaria'] || 0,
      abertosPorPortal['Financeiro'] || 0,
      abertosPorPortal['Ex-Aluno'] || 0
    ];
  }
  
  window.chartChamadosPorPortal = new Chart(ctxChamadosPorPortal, {
    type: "bar",
    data: {
      labels: chartData.labels,
      datasets: [{
        label: "Total de Chamados",
        data: chartData.totais,
        backgroundColor: "#001830",
        borderColor: "#ff5b00",
        borderWidth: 1
      }, {
        label: "Chamados Finalizados",
        data: chartData.finalizados,
        backgroundColor: "#0d6efd",
        borderColor: "#0a58ca",
        borderWidth: 1
      }, {
        label: "Chamados em Aberto",
        data: chartData.abertos,
        backgroundColor: "#ffc107",
        borderColor: "#e0a800",
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Ouvidoria
var ctxOuvidoriaStatus = document.getElementById("chartOuvidoriaStatus");
if (ctxOuvidoriaStatus) {
  if (ouvidoriaStatus.length > 0) {
    ctxOuvidoriaStatus = ctxOuvidoriaStatus.getContext("2d");
    var labelsOuvidoria = ouvidoriaStatus.map((item) => item.status);
    var dataOuvidoria = ouvidoriaStatus.map((item) => item.count);
    window.chartOuvidoriaStatus = new Chart(ctxOuvidoriaStatus, {
      type: "bar",
      data: {
        labels: labelsOuvidoria,
        datasets: [
          {
            label: "Solicitações",
            data: dataOuvidoria,
            backgroundColor: getStatusColors(labelsOuvidoria),
          },
        ],
      },
    });
  } else {
    ctxOuvidoriaStatus.style.display = 'none';
    const container = ctxOuvidoriaStatus.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

var ctxOuvidoriaTipos = document.getElementById("chartOuvidoriaTipos");
if (ctxOuvidoriaTipos) {
  if (ouvidoriaTipos.length > 0) {
    ctxOuvidoriaTipos = ctxOuvidoriaTipos.getContext("2d");
    var labelsOuvidoriaTipos = ouvidoriaTipos.map((item) => item.manifestacao || item.tipo || item.categoria);
    var dataOuvidoriaTipos = ouvidoriaTipos.map((item) => item.count);
    window.chartOuvidoriaTipos = new Chart(ctxOuvidoriaTipos, {
      type: "pie",
      data: {
        labels: labelsOuvidoriaTipos,
        datasets: [
          {
            data: dataOuvidoriaTipos,
            backgroundColor: getPieChartColors(dataOuvidoriaTipos.length),
          },
        ],
      },
      options: getPieChartOptions(),
    });
  } else {
    ctxOuvidoriaTipos.style.display = 'none';
    const container = ctxOuvidoriaTipos.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

// EAD
var ctxEADStatus = document.getElementById("chartEADStatus");
if (ctxEADStatus) {
  if (eadStatus.length > 0) {
    ctxEADStatus = ctxEADStatus.getContext("2d");
    var labelsEAD = eadStatus.map((item) => item.status);
    var dataEAD = eadStatus.map((item) => item.count);
    window.chartEADStatus = new Chart(ctxEADStatus, {
      type: "bar",
      data: {
        labels: labelsEAD,
        datasets: [
          {
            label: "Solicitações",
            data: dataEAD,
            backgroundColor: getStatusColors(labelsEAD),
          },
        ],
      },
    });
  } else {
    ctxEADStatus.style.display = 'none';
    const container = ctxEADStatus.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

var ctxEADServicos = document.getElementById("chartEADServicos");
if (ctxEADServicos) {
  if (eadServicos.length > 0) {
    ctxEADServicos = ctxEADServicos.getContext("2d");
    var labelsEADServ = eadServicos.map((item) => item.categoria || item.tipo || item.servico);
    var dataEADServ = eadServicos.map((item) => item.count);
    window.chartEADServicos = new Chart(ctxEADServicos, {
      type: "pie",
      data: {
        labels: labelsEADServ,
        datasets: [
          {
            data: dataEADServ,
            backgroundColor: getPieChartColors(),
            borderColor: getPieChartColors().map(color => color.replace('0.8', '1')),
            borderWidth: 2
          },
        ],
      },
      options: getPieChartOptions('Serviços EAD')
    });
  } else {
    ctxEADServicos.style.display = 'none';
    const container = ctxEADServicos.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

// Processo Seletivo
var ctxProcessoStatus = document.getElementById("chartProcessoStatus");
if (ctxProcessoStatus) {
  if (processoStatus.length > 0) {
    ctxProcessoStatus = ctxProcessoStatus.getContext("2d");
    var labelsProcesso = processoStatus.map((item) => item.status);
    var dataProcesso = processoStatus.map((item) => item.count);
    window.chartProcessoStatus = new Chart(ctxProcessoStatus, {
      type: "bar",
      data: {
        labels: labelsProcesso,
        datasets: [
          {
            label: "Solicitações",
            data: dataProcesso,
            backgroundColor: getStatusColors(labelsProcesso),
          },
        ],
      },
    });
  } else {
    ctxProcessoStatus.style.display = 'none';
    const container = ctxProcessoStatus.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

var ctxProcessoServicos = document.getElementById("chartProcessoServicos");
if (ctxProcessoServicos) {
  if (processoServicos.length > 0) {
    ctxProcessoServicos = ctxProcessoServicos.getContext("2d");
    var labelsProcessoServ = processoServicos.map((item) => item.categoria || item.tipo || item.servico);
    var dataProcessoServ = processoServicos.map((item) => item.count);
    window.chartProcessoServicos = new Chart(ctxProcessoServicos, {
      type: "pie",
      data: {
        labels: labelsProcessoServ,
        datasets: [
          {
            data: dataProcessoServ,
            backgroundColor: getPieChartColors(dataProcessoServ.length),
          },
        ],
      },
      options: getPieChartOptions(),
    });
  } else {
    ctxProcessoServicos.style.display = 'none';
    const container = ctxProcessoServicos.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

// Secretaria
var ctxSecretariaStatus = document.getElementById("chartSecretariaStatus");
if (ctxSecretariaStatus) {
  if (secretariaStatus.length > 0) {
    ctxSecretariaStatus = ctxSecretariaStatus.getContext("2d");
    var labelsSecretaria = secretariaStatus.map((item) => item.status);
    var dataSecretaria = secretariaStatus.map((item) => item.count);
    window.chartSecretariaStatus = new Chart(ctxSecretariaStatus, {
      type: "bar",
      data: {
        labels: labelsSecretaria,
        datasets: [
          {
            label: "Solicitações",
            data: dataSecretaria,
            backgroundColor: getStatusColors(labelsSecretaria),
          },
        ],
      },
    });
  } else {
    ctxSecretariaStatus.style.display = 'none';
    const container = ctxSecretariaStatus.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

var ctxSecretariaServicos = document.getElementById("chartSecretariaServicos");
if (ctxSecretariaServicos) {
  if (secretariaServicos.length > 0) {
    ctxSecretariaServicos = ctxSecretariaServicos.getContext("2d");
    var labelsSecretariaServ = secretariaServicos.map((item) => {
      return item.categoria || "Categoria não definida";
    });
    var dataSecretariaServ = secretariaServicos.map((item) => item.count);
    window.chartSecretariaServicos = new Chart(ctxSecretariaServicos, {
      type: "pie",
      data: {
        labels: labelsSecretariaServ,
        datasets: [
          {
            data: dataSecretariaServ,
            backgroundColor: getPieChartColors(dataSecretariaServ.length),
          },
        ],
      },
      options: getPieChartOptions(),
    });
  } else {
    ctxSecretariaServicos.style.display = 'none';
    const container = ctxSecretariaServicos.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

// Financeiro
var ctxFinanceiroStatus = document.getElementById("chartFinanceiroStatus");
if (ctxFinanceiroStatus) {
  if (financeiroStatus.length > 0) {
    ctxFinanceiroStatus = ctxFinanceiroStatus.getContext("2d");
    var labelsFinanceiro = financeiroStatus.map((item) => item.status);
    var dataFinanceiro = financeiroStatus.map((item) => item.count);
    window.chartFinanceiroStatus = new Chart(ctxFinanceiroStatus, {
      type: "bar",
      data: {
        labels: labelsFinanceiro,
        datasets: [
          {
            label: "Solicitações",
            data: dataFinanceiro,
            backgroundColor: getStatusColors(labelsFinanceiro),
          },
        ],
      },
    });
  } else {
    ctxFinanceiroStatus.style.display = 'none';
    const container = ctxFinanceiroStatus.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

var ctxFinanceiroServicos = document.getElementById("chartFinanceiroServicos");
if (ctxFinanceiroServicos) {
  if (financeiroServicos.length > 0) {
    ctxFinanceiroServicos = ctxFinanceiroServicos.getContext("2d");
    var labelsFinanceiroServ = financeiroServicos.map((item) => item.categoria || item.tipo || item.servico);
    var dataFinanceiroServ = financeiroServicos.map((item) => item.count);
    window.chartFinanceiroServicos = new Chart(ctxFinanceiroServicos, {
      type: "pie",
      data: {
        labels: labelsFinanceiroServ,
        datasets: [
          {
            data: dataFinanceiroServ,
            backgroundColor: getPieChartColors(dataFinanceiroServ.length),
          },
        ],
      },
      options: getPieChartOptions(),
    });
  } else {
    ctxFinanceiroServicos.style.display = 'none';
    const container = ctxFinanceiroServicos.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

// Ex-Aluno
var ctxExAlunoStatus = document.getElementById("chartExAlunoStatus");
if (ctxExAlunoStatus) {
  if (exalunoStatus.length > 0) {
    ctxExAlunoStatus = ctxExAlunoStatus.getContext("2d");
    var labelsExAluno = exalunoStatus.map((item) => item.status);
    var dataExAluno = exalunoStatus.map((item) => item.count);
    window.chartExAlunoStatus = new Chart(ctxExAlunoStatus, {
      type: "bar",
      data: {
        labels: labelsExAluno,
        datasets: [
          {
            label: "Solicitações",
            data: dataExAluno,
            backgroundColor: getStatusColors(labelsExAluno),
          },
        ],
      },
    });
  } else {
    ctxExAlunoStatus.style.display = 'none';
    const container = ctxExAlunoStatus.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

var ctxExAlunoServicos = document.getElementById("chartExAlunoServicos");
if (ctxExAlunoServicos) {
  if (exalunoServicos.length > 0) {
    ctxExAlunoServicos = ctxExAlunoServicos.getContext("2d");
    var labelsExAlunoServ = exalunoServicos.map((item) => item.manifestacao || item.categoria || item.tipo || item.servico);
    var dataExAlunoServ = exalunoServicos.map((item) => item.count);
    window.chartExAlunoServicos = new Chart(ctxExAlunoServicos, {
      type: "pie",
      data: {
        labels: labelsExAlunoServ,
        datasets: [
          {
            data: dataExAlunoServ,
            backgroundColor: getPieChartColors(dataExAlunoServ.length),
          },
        ],
      },
      options: getPieChartOptions(),
    });
  } else {
    ctxExAlunoServicos.style.display = 'none';
    const container = ctxExAlunoServicos.parentElement;
    const noDataMsg = document.createElement('p');
    noDataMsg.className = 'no-data';
    noDataMsg.textContent = 'Nenhum dado encontrado para o período selecionado.';
    container.appendChild(noDataMsg);
  }
}

// ========== FUNÇÕES DE FILTRO ==========

// Variáveis globais para armazenar filtros atuais
let currentFilters = {
    periodo: 'todos',
    portal: 'todos'
};

// Função para obter valores dos filtros
function getFilterValues() {
    const periodoSelect = document.getElementById('periodFilter');
    const portalSelect = document.getElementById('portalFilter');

    // Mapear valores do HTML para os valores esperados pelo backend
    const periodoMapping = {
        'all': 'todos',
        'today': 'hoje',
        '7d': 'semana',
        '30d': 'mes',
        '90d': 'trimestre',
        '365d': 'ano'
    };

    const portalMapping = {
        'all': 'todos',
        'ouvidoria': 'ouvidoria',
        'ead': 'ead',
        'processo': 'processo_seletivo',
        'secretaria': 'secretaria',
        'financeiro': 'financeiro',
        'exaluno': 'exaluno'
    };

    return {
        periodo: periodoSelect ? periodoMapping[periodoSelect.value] || 'todos' : 'todos',
        portal: portalSelect ? portalMapping[portalSelect.value] || 'todos' : 'todos'
    };
}

// Função para aplicar filtros
function applyFilters() {
    // Obter valores dos filtros
    const filters = getFilterValues();
    currentFilters = filters;

    // Redirecionar com parâmetros de filtro
    const url = new URL(window.location);
    url.searchParams.set('periodo', filters.periodo);
    url.searchParams.set('portal', filters.portal);
    
    // Preservar a aba ativa
    const activeTab = document.querySelector('.tab-button.active');
    const tabValue = activeTab ? activeTab.getAttribute('data-tab') : 'consolidado';
    url.searchParams.set('tab', tabValue);
    
    window.location.href = url.toString();
}

// Função para resetar filtros
function resetFilters() {
    // Redirecionar para URL sem parâmetros
    const url = new URL(window.location);
    url.search = ''; // Limpar todos os parâmetros
    window.location.href = url.toString();
}

// Função para mostrar estado de carregamento
function showLoadingState() {
    const filterBar = document.querySelector('.filter-bar');
    if (filterBar) {
        filterBar.classList.add('loading');
    }

    // Desabilitar botões durante carregamento
    const applyBtn = document.getElementById('btn-aplicar-filtros');
    const resetBtn = document.getElementById('btn-resetar-filtros');

    if (applyBtn) applyBtn.disabled = true;
    if (resetBtn) resetBtn.disabled = true;
}

// Função para esconder estado de carregamento
function hideLoadingState() {
    const filterBar = document.querySelector('.filter-bar');
    if (filterBar) {
        filterBar.classList.remove('loading');
    }

    // Reabilitar botões
    const applyBtn = document.getElementById('btn-aplicar-filtros');
    const resetBtn = document.getElementById('btn-resetar-filtros');

    if (applyBtn) applyBtn.disabled = false;
    if (resetBtn) resetBtn.disabled = false;
}

// Função AJAX para buscar dados filtrados
async function fetchFilteredData(filters) {
    const formData = new FormData();
    formData.append('periodo', filters.periodo);
    formData.append('portal', filters.portal);

    const response = await fetch('get_filtered_data.php', {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        throw new Error('Erro na requisição: ' + response.status);
    }

    return await response.json();
}

// Função AJAX para buscar lista de chamados
async function fetchChamadosData(filters, pagina = 1) {
    const formData = new FormData();
    formData.append('acao', 'get_chamados');  // CORRIGIDO: era 'action'
    formData.append('portal', filters.portal);
    formData.append('status', filters.status);
    formData.append('pagina', pagina);
    formData.append('limite', 50);

    const response = await fetch('get_filtered_data.php', {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        throw new Error('Erro na requisição: ' + response.status);
    }

    return await response.json();
}

// Função para renderizar tabela de chamados
function renderChamadosTable(data) {
    const tbody = document.getElementById('chamadosTableBody');
    const pagination = document.getElementById('paginationContainer');
    
    if (!tbody || !pagination) {
        return;
    }
    
    // Limpar tabela
    tbody.innerHTML = '';
    
    if (!data.chamados || data.chamados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhum chamado encontrado</td></tr>';
        pagination.innerHTML = '';
        return;
    }
    
    // Renderizar linhas da tabela
    data.chamados.forEach(chamado => {
        const row = document.createElement('tr');
        
        // Status badge com cores específicas
        let statusClass = 'status-secondary';
        let statusText = chamado.status;
        
        const statusLower = chamado.status.toLowerCase();
        if (statusLower.includes('finalizado')) {
            statusClass = 'status-finalizado';
        } else if (statusLower.includes('transferido')) {
            statusClass = 'status-transferido';
        } else if (statusLower.includes('respondido drumond') || statusLower.includes('respondido drummond')) {
            statusClass = 'status-respondido-drumond';
        } else if (statusLower.includes('em análise') || statusLower.includes('em analise')) {
            statusClass = 'status-em-analise';
        } else if (statusLower.includes('respondido')) {
            statusClass = 'status-respondido';
        } else if (statusLower.includes('aberto') || statusLower.includes('pendente') || statusLower.includes('novo')) {
            statusClass = 'status-warning';
        } else if (statusLower.includes('fechado') || statusLower.includes('concluido') || statusLower.includes('resolvido')) {
            statusClass = 'status-success';
        } else if (statusLower.includes('andamento') || statusLower.includes('em progresso') || statusLower.includes('processando')) {
            statusClass = 'status-primary';
        } else if (statusLower.includes('cancelado') || statusLower.includes('rejeitado')) {
            statusClass = 'status-danger';
        } else if (statusLower.includes('aguardando') || statusLower.includes('pausado')) {
            statusClass = 'status-info';
        }
        
        row.innerHTML = `
            <td>${chamado.portal}</td>
            <td><span class="badge ${statusClass}">${statusText}</span></td>
            <td>${new Date(chamado.data_abertura).toLocaleDateString('pt-BR')}</td>
            <td>${chamado.data_fechamento ? new Date(chamado.data_fechamento).toLocaleDateString('pt-BR') : 'Em andamento'}</td>
            <td>${chamado.servico || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="verDetalhesChamado(${chamado.id})" title="Ver Detalhes">
                    <i class="fas fa-eye"></i> Ver Detalhes
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Renderizar paginação
    renderChamadosPagination(data);
}

// Função para renderizar paginação
function renderChamadosPagination(data) {
    const pagination = document.getElementById('paginationContainer');
    if (!pagination) return;
    
    // Se não há múltiplas páginas, esconder paginação
    if (data.total_paginas <= 1) {
        pagination.style.display = 'none';
        return;
    }
    
    pagination.style.display = 'flex';
    
    // Atualizar informações da página
    const pageInfo = document.getElementById('pageInfo');
    if (pageInfo) {
        pageInfo.textContent = `Página ${data.pagina} de ${data.total_paginas}`;
    }
    
    // Habilitar/desabilitar botões
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (prevBtn) {
        prevBtn.disabled = data.pagina <= 1;
        prevBtn.onclick = () => loadChamadosPage(data.pagina - 1);
    }
    
    if (nextBtn) {
        nextBtn.disabled = data.pagina >= data.total_paginas;
        nextBtn.onclick = () => loadChamadosPage(data.pagina + 1);
    }
}

// Função para carregar página específica de chamados
async function loadChamadosPage(pagina) {
    const portalFilter = document.getElementById('portalFilterChamados');
    const statusFilter = document.getElementById('statusFilterChamados');
    
    const filters = {
        portal: portalFilter ? portalFilter.value : 'todos',
        status: statusFilter ? statusFilter.value : 'todos'
    };
    
    showLoadingState();
    
    try {
        const data = await fetchChamadosData(filters, pagina);
        renderChamadosTable(data);
    } catch (error) {
        console.error('Erro ao carregar chamados:', error);
        const tbody = document.getElementById('chamadosTableBody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar dados</td></tr>';
        }
    } finally {
        hideLoadingState();
    }
}

// Função para aplicar filtros de chamados
function applyChamadosFilters() {
    loadChamadosPage(1);
}

// Função para resetar filtros de chamados
function resetChamadosFilters() {
    const portalFilter = document.getElementById('portalFilterChamados');
    const statusFilter = document.getElementById('statusFilterChamados');
    
    if (portalFilter) portalFilter.value = 'todos';
    if (statusFilter) statusFilter.value = 'todos';
    
    loadChamadosPage(1);
}

// Função para definir valores dos filtros baseado na URL
function setFilterValuesFromURL() {
    const urlParams = new URLSearchParams(window.location.search);

    const periodo = urlParams.get('periodo') || 'todos';
    const portal = urlParams.get('portal') || 'todos';

    // Mapear valores internos para valores do HTML
    const periodoReverseMapping = {
        'todos': 'all',
        'semana': '7d',
        'mes': '30d',
        'trimestre': '90d',
        'ano': '365d'
    };

    const portalReverseMapping = {
        'todos': 'all',
        'ouvidoria': 'ouvidoria',
        'ead': 'ead',
        'processo_seletivo': 'processo',
        'secretaria': 'secretaria',
        'financeiro': 'financeiro',
        'exaluno': 'exaluno'
    };

    const periodoSelect = document.getElementById('periodFilter');
    const portalSelect = document.getElementById('portalFilter');

    if (periodoSelect) {
        periodoSelect.value = periodoReverseMapping[periodo] || 'all';
    }

    if (portalSelect) {
        portalSelect.value = portalReverseMapping[portal] || 'all';
    }

    // Se há filtros aplicados, mostrar a aba apropriada
    if (portal !== 'todos') {
        // Pequeno delay para garantir que o DOM esteja pronto
        setTimeout(() => {
            if (typeof showTab === 'function') {
                showTab('portais');
            }
        }, 100);
    }

    currentFilters = { periodo, portal };
}

// ========== EVENT LISTENERS ==========

// Função para ver detalhes do chamado
function verDetalhesChamado(chamadoId) {
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('chamadoModal'));
    modal.show();

    // Fazer requisição para obter detalhes do chamado
    fetch('get_chamado_detalhes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'chamado_id=' + chamadoId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarDetalhesChamado(data.chamado);
        } else {
            document.getElementById('chamadoModalContent').innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erro ao carregar detalhes do chamado: ${data.message || 'Erro desconhecido'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        document.getElementById('chamadoModalContent').innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Erro de comunicação com o servidor. Tente novamente.
            </div>
        `;
    });
}

// Função para mostrar os detalhes do chamado no modal
function mostrarDetalhesChamado(chamado) {
    const statusClass = getStatusClass(chamado.status);
    const statusLower = chamado.status.toLowerCase();
    const isFinalizado = statusLower.includes('finalizado') || statusLower.includes('fechado');
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-info-circle me-2"></i>Informações do Chamado
                </h6>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold">ID:</td>
                        <td>${chamado.id}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Portal:</td>
                        <td>${chamado.portal}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status:</td>
                        <td><span class="badge ${statusClass}">${chamado.status}</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Data Abertura:</td>
                        <td>${new Date(chamado.data_abertura).toLocaleDateString('pt-BR')}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Data Fechamento:</td>
                        <td>${chamado.data_encerramento ? new Date(chamado.data_encerramento).toLocaleDateString('pt-BR') : 'Em andamento'}</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-user me-2"></i>Dados do Solicitante
                </h6>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold">Nome:</td>
                        <td>${chamado.nome || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Email:</td>
                        <td>${chamado.email || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Telefone:</td>
                        <td>${chamado.telefone || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Matrícula:</td>
                        <td>${chamado.matricula || 'N/A'}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        ${isFinalizado ? `
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="fw-bold text-success mb-3">
                    <i class="fas fa-check-circle me-2"></i>Dados do Administrador
                </h6>
                <div class="border rounded p-3 bg-light">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="fw-bold" style="width: 150px;">Administrador:</td>
                            <td>${chamado.admin_nome || chamado.responsavel || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email:</td>
                            <td>${chamado.admin_email || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Data de Fechamento:</td>
                            <td>${chamado.data_encerramento ? new Date(chamado.data_encerramento).toLocaleDateString('pt-BR') : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Observações:</td>
                            <td>${chamado.observacoes || chamado.comentarios || 'N/A'}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        ` : ''}
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-comment me-2"></i>Descrição do Chamado
                </h6>
                <div class="border rounded p-3 bg-light">
                    ${chamado.descricao || chamado.manifestacao || chamado.categoria || 'Nenhuma descrição disponível.'}
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('chamadoModalContent').innerHTML = html;
}

// Função auxiliar para obter a classe CSS do status
function getStatusClass(status) {
    const statusLower = status.toLowerCase();
    if (statusLower.includes('finalizado')) {
        return 'status-finalizado';
    } else if (statusLower.includes('transferido')) {
        return 'status-transferido';
    } else if (statusLower.includes('respondido drumond') || statusLower.includes('respondido drummond')) {
        return 'status-respondido-drumond';
    } else if (statusLower.includes('em análise') || statusLower.includes('em analise')) {
        return 'status-em-analise';
    } else if (statusLower.includes('respondido')) {
        return 'status-respondido';
    } else if (statusLower.includes('aberto') || statusLower.includes('pendente') || statusLower.includes('novo')) {
        return 'status-warning';
    } else if (statusLower.includes('fechado') || statusLower.includes('concluido') || statusLower.includes('resolvido')) {
        return 'status-success';
    } else if (statusLower.includes('andamento') || statusLower.includes('em progresso') || statusLower.includes('processando')) {
        return 'status-primary';
    } else if (statusLower.includes('cancelado') || statusLower.includes('rejeitado')) {
        return 'status-danger';
    } else if (statusLower.includes('aguardando') || statusLower.includes('pausado')) {
        return 'status-info';
    }
    return 'status-secondary';
}

// Adicionar event listeners quando DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Definir valores dos filtros baseado na URL
    setFilterValuesFromURL();

    // Botão Aplicar Filtros
    const applyBtn = document.getElementById('applyFilters');
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            applyFilters();
        });
    } else {
        console.error('Botão applyFilters não encontrado!');
    }

    // Botão Resetar Filtros
    const resetBtn = document.getElementById('resetFilters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            resetFilters();
        });
    } else {
        console.error('Botão resetFilters não encontrado!');
    }

    // Selects
    const periodoSelect = document.getElementById('periodFilter');
    const portalSelect = document.getElementById('portalFilter');

    // Filtros da aba Chamados
    const chamadosPortalFilter = document.getElementById('portalFilterChamados');
    const chamadosStatusFilter = document.getElementById('statusFilterChamados');
    const applyChamadosBtn = document.getElementById('applyFiltersChamados');
    const resetChamadosBtn = document.getElementById('resetFiltersChamados');

    if (chamadosPortalFilter) {
        chamadosPortalFilter.addEventListener('change', function() {
            applyChamadosFilters();
        });
    }

    if (chamadosStatusFilter) {
        chamadosStatusFilter.addEventListener('change', function() {
            applyChamadosFilters();
        });
    }

    if (applyChamadosBtn) {
        applyChamadosBtn.addEventListener('click', function() {
            applyChamadosFilters();
        });
    }

    if (resetChamadosBtn) {
        resetChamadosBtn.addEventListener('click', function() {
            resetChamadosFilters();
        });
    }

    // Carregar chamados iniciais quando a aba for ativada
    const chamadosTab = document.querySelector('[data-tab="chamados"]');
    if (chamadosTab) {
        chamadosTab.addEventListener('click', function() {
            // Pequeno delay para garantir que a aba esteja ativa
            setTimeout(() => {
                loadChamadosPage(1);
            }, 100);
        });
    }
});

// Verificar se a aba de chamados já está ativa após um delay maior
setTimeout(() => {
    const activeTab = document.querySelector('.tab-button.active');
    const activeTabValue = activeTab ? activeTab.getAttribute('data-tab') : null;
    if (activeTabValue === 'chamados') {
        loadChamadosPage(1);
    }
}, 500);
