-- Run in Supabase SQL Editor
CREATE TABLE public.configuraciones (
  clave text PRIMARY KEY,
  valor text
);

-- Default Values
INSERT INTO public.configuraciones (clave, valor) VALUES 
 ('inscripciones_abiertas', 'true'), 
 ('carga_notas_abierta', 'true'),
 ('edicion_horarios_abierta', 'true'),
 ('nombre_sede', 'CBUH - Extensión Higuerote'),
 ('periodo_actual', '2025-2026')
ON CONFLICT DO NOTHING;

-- Enable RLS (Optional but recommended)
ALTER TABLE public.configuraciones ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Enable read/write for auth users" ON "public"."configuraciones" USING (auth.role() = 'authenticated') WITH CHECK (auth.role() = 'authenticated');
