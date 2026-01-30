<?php
/**
 * Servicio para gestión lógica de estudiantes
 * Encapsula consultas de base de datos relacionadas con alumnos
 */
class StudentService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene estadísticas generales para el Dashboard
     */
    public function getDashboardStats()
    {
        $sql = "
            SELECT 
                (SELECT COUNT(*) FROM estudiantes WHERE estado_id = 1) as total,
                (SELECT COUNT(*) FROM estudiantes WHERE estado_id = 1 AND creado_el >= NOW() - INTERVAL '30 days') as nuevos,
                (SELECT COUNT(DISTINCT estudiante_id) FROM inscripciones WHERE estado_id = 1) as inscritos,
                (SELECT COUNT(*) FROM estudiantes WHERE año_actual = (SELECT MAX(año_actual) FROM estudiantes) AND estado_id = 1) as graduandos
        ";
        $stats = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

        return [
            'total' => $stats['total'] ?: 0,
            'nuevos' => $stats['nuevos'] ?: 0,
            'inscritos' => $stats['inscritos'] ?: 0,
            'graduandos' => $stats['graduandos'] ?: 0
        ];
    }

    /**
     * Obtiene listado de documentos pendientes de verificación
     */
    public function getPendingDocuments($limit = 10)
    {
        $sql = "
            SELECT 
                e.nombres, e.apellidos, e.cedula, e.año_actual, 
                d.tipo_documento, 
                d.id as doc_id
            FROM documentos_estudiantes d
            JOIN estudiantes e ON d.estudiante_id = e.id
            WHERE d.verificado = FALSE AND d.estado_id = 1
            ORDER BY d.creado_el DESC
            LIMIT :limit
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene actividad reciente (notas actualizadas)
     */
    public function getRecentActivity($limit = 5)
    {
        $sql = "
            SELECT 
                n.actualizado_el,
                m.nombre as materia,
                e.nombres, e.apellidos
            FROM notas n
            JOIN inscripciones i ON n.inscripcion_id = i.id
            JOIN estudiantes e ON i.estudiante_id = e.id
            JOIN cargas_academicas ca ON i.carga_academica_id = ca.id
            JOIN materias m ON ca.materia_id = m.id
            WHERE n.estado_id = 1
            ORDER BY n.actualizado_el DESC
            LIMIT :limit
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
