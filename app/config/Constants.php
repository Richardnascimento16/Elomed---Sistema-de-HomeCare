<?php

/**
 * Constants do Sistema
 * Define valores constantes para status, perfis e outras configurações
 */

class Constants {
    // Perfis de usuário
    const PERFIL_ADMIN = 'admin';
    const PERFIL_MEDICO = 'medico';
    const PERFIL_ENFERMEIRO = 'enfermeiro';
    const PERFIL_TECNICO_ENFERMAGEM = 'tecnico_enfermagem';
    const PERFIL_PACIENTE = 'paciente';
    
    // Status de usuário
    const STATUS_ATIVO = 'ativo';
    const STATUS_DESATIVADO = 'desativado';
    const STATUS_BLOQUEADO = 'bloqueado';
    
    // Status de agendamento
    const AGENDAMENTO_PENDENTE = 'pendente';
    const AGENDAMENTO_AGENDADO = 'agendado';
    const AGENDAMENTO_EM_ANDAMENTO = 'em_andamento';
    const AGENDAMENTO_CONCLUIDO = 'concluido';
    const AGENDAMENTO_CANCELADO = 'cancelado';
    
    // Status de triagem
    const TRIAGEM_PENDENTE = 'pendente';
    const TRIAGEM_APROVADA = 'aprovada';
    const TRIAGEM_REJEITADA = 'rejeitada';
    
    // Status de visita
    const VISITA_EM_ANDAMENTO = 'em_andamento';
    const VISITA_CONCLUIDO = 'concluido';
    
    // Categorias de protocolo
    const PROTOCOLO_CATEGORIA_AGUDO = 'agudo';
    const PROTOCOLO_CATEGORIA_CRONICO = 'cronico';
    const PROTOCOLO_CATEGORIA_PREVENTIVO = 'preventivo';
    
    // Tipos de pergunta de protocolo
    const PERGUNTA_TIPO_TEXTO = 'texto';
    const PERGUNTA_TIPO_NUMERO = 'numero';
    const PERGUNTA_TIPO_TEXTAREA = 'textarea';
    const PERGUNTA_TIPO_SELECT = 'select';
    const PERGUNTA_TIPO_RADIO = 'radio';
    const PERGUNTA_TIPO_CHECKBOX = 'checkbox';
    
    // Níveis de log
    const LOG_LEVEL_DEBUG = 'debug';
    const LOG_LEVEL_INFO = 'info';
    const LOG_LEVEL_WARNING = 'warning';
    const LOG_LEVEL_ERROR = 'error';
    const LOG_LEVEL_CRITICAL = 'critical';
    
    // Ações de auditoria
    const AUDITORIA_ACAO_LOGIN = 'login';
    const AUDITORIA_ACAO_LOGOUT = 'logout';
    const AUDITORIA_ACAO_CRIAR_USUARIO = 'criar_usuario';
    const AUDITORIA_ACAO_ATUALIZAR_USUARIO = 'atualizar_usuario';
    const AUDITORIA_ACAO_DESATIVAR_USUARIO = 'desativar_usuario';
    const AUDITORIA_ACAO_CRIAR_AGENDAMENTO = 'criar_agendamento';
    const AUDITORIA_ACAO_ATUALIZAR_AGENDAMENTO = 'atualizar_agendamento';
    const AUDITORIA_ACAO_CRIAR_PRONTUARIO = 'criar_prontuario';
    const AUDITORIA_ACAO_ATUALIZAR_PRONTUARIO = 'atualizar_prontuario';
    const AUDITORIA_ACAO_GERAR_DIAGNOSTICO_IA = 'gerar_diagnostico_ia';
    const AUDITORIA_ACAO_USO_IA = 'uso_ia';
    const AUDITORIA_ACAO_CRIAR_TRIAGEM = 'criar_triagem';
    
    // Configurações de sessão
    const SESSION_LIFETIME = 3600; // 1 hora em segundos
    const SESSION_NAME = 'conexau_session';
    
    // Configurações de paginação
    const PAGINATION_DEFAULT_LIMIT = 20;
    const PAGINATION_MAX_LIMIT = 100;
    
    // Configurações de cache
    const CACHE_LIFETIME_SHORT = 300; // 5 minutos
    const CACHE_LIFETIME_MEDIUM = 1800; // 30 minutos
    const CACHE_LIFETIME_LONG = 3600; // 1 hora
}
