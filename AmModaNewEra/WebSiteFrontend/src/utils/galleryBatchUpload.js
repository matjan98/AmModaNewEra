export const GALLERY_UPLOAD_BATCH_SIZE = 10
export const GALLERY_UPLOAD_BATCH_TIMEOUT_MS = 300_000

/**
 * @param {FileList | File[]} files
 * @param {number} [batchSize]
 * @returns {File[][]}
 */
export function chunkFiles(files, batchSize = GALLERY_UPLOAD_BATCH_SIZE) {
  const list = Array.from(files)
  const chunks = []
  for (let i = 0; i < list.length; i += batchSize) {
    chunks.push(list.slice(i, i + batchSize))
  }
  return chunks
}

/**
 * @param {File[]} batch
 * @returns {FormData}
 */
function buildBatchFormData(batch) {
  const formData = new FormData()
  batch.forEach((file) => formData.append('photos[]', file))
  return formData
}

/**
 * @param {string[]} sessionUploadedIds
 * @param {(path: string, formData: FormData) => Promise<{ ok: boolean, data?: { ok?: boolean } }>} deleteForm
 * @returns {Promise<string[]>}
 */
async function rollbackSessionUploads(sessionUploadedIds, deleteForm) {
  const rollbackFailedIds = []

  for (const id of sessionUploadedIds) {
    const formData = new FormData()
    formData.append('id', id)

    try {
      const res = await deleteForm('api/delete.php', formData)
      if (!res.ok || res.data?.ok !== true) {
        rollbackFailedIds.push(id)
      }
    } catch {
      rollbackFailedIds.push(id)
    }
  }

  return rollbackFailedIds
}

/**
 * @param {{ reason: string, rollbackFailedIds: string[] }} params
 * @returns {{ ok: false, error: string, rollbackFailedIds?: string[] }}
 */
function buildFailureResult({ reason, rollbackFailedIds }) {
  const rollbackOk = rollbackFailedIds.length === 0
  const error = rollbackOk
    ? `Upload przerwany: ${reason} Wgrane zdjęcia zostały cofnięte.`
    : `Upload przerwany: ${reason} Nie udało się usunąć ${rollbackFailedIds.length} zdjęć — odśwież galerię.`

  return {
    ok: false,
    error,
    rollbackFailedIds: rollbackOk ? undefined : rollbackFailedIds,
  }
}

/**
 * @param {{
 *   files: FileList | File[],
 *   postForm: (path: string, formData: FormData, options?: { timeoutMs?: number }) => Promise<{ ok: boolean, data?: { ok?: boolean, uploaded?: string[], error?: string } }>,
 *   deleteForm: (path: string, formData: FormData) => Promise<{ ok: boolean, data?: { ok?: boolean } }>,
 *   onProgress?: (progress: { uploadedCount: number, totalCount: number, batchIndex: number, batchCount: number }) => void,
 *   uploadTimeoutMs?: number,
 *   batchSize?: number,
 * }} params
 * @returns {Promise<{ ok: true, uploadedCount: number } | { ok: false, error: string, rollbackFailedIds?: string[] }>}
 */
export async function uploadPhotosInBatches({
  files,
  postForm,
  deleteForm,
  onProgress,
  uploadTimeoutMs = GALLERY_UPLOAD_BATCH_TIMEOUT_MS,
  batchSize = GALLERY_UPLOAD_BATCH_SIZE,
}) {
  const batches = chunkFiles(files, batchSize)
  const totalCount = batches.reduce((sum, batch) => sum + batch.length, 0)
  const batchCount = batches.length
  const sessionUploadedIds = []
  let uploadedCount = 0

  onProgress?.({ uploadedCount: 0, totalCount, batchIndex: 0, batchCount })

  for (let batchIndex = 0; batchIndex < batches.length; batchIndex++) {
    const batch = batches[batchIndex]
    let res

    try {
      res = await postForm('api/upload.php', buildBatchFormData(batch), { timeoutMs: uploadTimeoutMs })
    } catch (error) {
      const rollbackFailedIds = await rollbackSessionUploads(sessionUploadedIds, deleteForm)
      const reason = error instanceof Error ? error.message : 'Błąd sieci podczas uploadu.'
      return buildFailureResult({ reason, rollbackFailedIds })
    }

    const uploaded = Array.isArray(res.data?.uploaded) ? res.data.uploaded : []
    const batchSucceeded = res.ok && res.data?.ok === true && uploaded.length === batch.length

    if (!batchSucceeded) {
      sessionUploadedIds.push(...uploaded)
      const rollbackFailedIds = await rollbackSessionUploads(sessionUploadedIds, deleteForm)

      let reason = res.data?.error
      if (!reason) {
        reason = uploaded.length < batch.length
          ? 'Nie wszystkie pliki w paczce zostały zapisane.'
          : 'Upload nie powiódł się.'
      }

      return buildFailureResult({ reason, rollbackFailedIds })
    }

    sessionUploadedIds.push(...uploaded)
    uploadedCount += uploaded.length
    onProgress?.({
      uploadedCount,
      totalCount,
      batchIndex: batchIndex + 1,
      batchCount,
    })
  }

  return { ok: true, uploadedCount }
}
