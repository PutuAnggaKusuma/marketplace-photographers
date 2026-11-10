<script>
window.formatFileSizeMB = function (bytes) {
  const mb = bytes / (1024 * 1024);
  return mb.toFixed(1).replace('.', ',') + ' MB';
};

window.validateUploadFiles = function (inputOrFiles, options = {}) {
  const isInput = inputOrFiles && typeof inputOrFiles === 'object' && 'files' in inputOrFiles;
  const inputEl = isInput ? inputOrFiles : null;
  const rawFiles = Array.from(isInput ? (inputOrFiles.files || []) : (inputOrFiles || []));

  const maxMB = Number(options.maxMB || 10);
  const maxBytes = maxMB * 1024 * 1024;
  const multiple = Boolean(options.multiple);
  const maxFiles = options.maxFiles ? Number(options.maxFiles) : null;

  const allowedMimes = options.allowedMimes !== undefined
    ? options.allowedMimes
    : ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
  const allowedExts = options.allowedExts !== undefined
    ? options.allowedExts
    : ['jpg', 'jpeg', 'png', 'webp'];
  const formatLabel = options.formatLabel || 'JPG, PNG, atau WEBP';

  const validFiles = [];
  const rejected = [];

  for (const file of rawFiles) {
    const ext = (file.name.split('.').pop() || '').toLowerCase();
    const mime = (file.type || '').toLowerCase();

    if (allowedExts.length > 0 || allowedMimes.length > 0) {
      const extOk = allowedExts.length > 0 && allowedExts.includes(ext);
      const mimeOk = allowedMimes.length > 0 && allowedMimes.includes(mime);
      if (!extOk && !mimeOk) {
        rejected.push({
          file,
          name: file.name,
          sizeText: window.formatFileSizeMB(file.size),
          reason: 'format harus ' + formatLabel
        });
        continue;
      }
    }

    if (file.size > maxBytes) {
      rejected.push({
        file,
        name: file.name,
        sizeText: window.formatFileSizeMB(file.size),
        reason: 'melebihi ' + maxMB + ' MB (' + window.formatFileSizeMB(file.size) + ')'
      });
      continue;
    }

    validFiles.push(file);
  }

  let finalValidFiles = validFiles;
  let overflowCount = 0;
  if (multiple && maxFiles && validFiles.length > maxFiles) {
    overflowCount = validFiles.length - maxFiles;
    finalValidFiles = validFiles.slice(0, maxFiles);
  }

  if (inputEl) {
    if (finalValidFiles.length === 0) {
      inputEl.value = '';
    } else if (multiple && typeof DataTransfer !== 'undefined') {
      const dt = new DataTransfer();
      finalValidFiles.forEach(f => dt.items.add(f));
      inputEl.files = dt.files;
    }
  }

  let errorMessage = '';
  if (!multiple) {
    if (rejected.length > 0) {
      const item = rejected[0];
      if (item.reason.startsWith('format harus')) {
        errorMessage = 'Format file harus ' + formatLabel + '.';
      } else {
        errorMessage = 'Ukuran file maksimal ' + maxMB + ' MB (file dipilih: ' + item.sizeText + ').';
      }
    }
  } else {
    const parts = [];
    if (rejected.length === 1) {
      parts.push('File ' + rejected[0].name + ' ditolak: ' + rejected[0].reason + '.');
    } else if (rejected.length > 1) {
      const list = rejected.map(r => r.name + ' (' + r.sizeText + ')').join(', ');
      parts.push(rejected.length + ' file ditolak: ' + list + '. Batas maksimal ' + maxMB + ' MB per file (' + formatLabel + ').');
    }
    if (overflowCount > 0) {
      parts.push('Maksimal ' + maxFiles + ' file yang dapat dipilih sekaligus.');
    }
    errorMessage = parts.join(' ');
  }

  return {
    validFiles: finalValidFiles,
    rejected,
    hasErrors: rejected.length > 0 || overflowCount > 0,
    errorMessage
  };
};
</script>

