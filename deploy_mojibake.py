import os
import time
import ftplib

def upload_files():
    # 1. Find the 39 files we just modified
    now = time.time()
    files_to_upload = []
    for root, dirs, files in os.walk('app'):
        for f in files:
            if f.endswith('.php') or f.endswith('.sql'):
                path = os.path.join(root, f).replace('\\', '/')
                mtime = os.path.getmtime(path)
                if now - mtime < 20 * 60:
                    files_to_upload.append(path)

    for root, dirs, files in os.walk('scripts'):
        for f in files:
            if f.endswith('.php') or f.endswith('.sql'):
                path = os.path.join(root, f).replace('\\', '/')
                mtime = os.path.getmtime(path)
                if now - mtime < 20 * 60:
                    files_to_upload.append(path)

    print(f"Found {len(files_to_upload)} modified files to deploy.")

    # 2. Upload them via FTP
    ftp = ftplib.FTP('ftp.casjoe.com', 'app@casjoe.com', 'app@casjoe.com')
    print("Connected to FTP.")
    
    success = 0
    failed = 0

    for file_path in files_to_upload:
        remote_path = '/' + file_path
        remote_dir = os.path.dirname(remote_path)
        
        # Ensure remote directory exists
        dirs = remote_dir.strip('/').split('/')
        current_dir = ''
        for d in dirs:
            if not current_dir:
                current_dir = '/' + d
            else:
                current_dir = current_dir + '/' + d
            try:
                ftp.mkd(current_dir)
            except ftplib.error_perm as e:
                # Directory already exists, ignore
                pass
        
        # Upload file
        try:
            with open(file_path, 'rb') as f:
                ftp.storbinary(f"STOR {remote_path}", f)
            print(f"[SUCCESS] Uploaded {file_path}")
            success += 1
        except Exception as e:
            print(f"[ERROR] Failed to upload {file_path}: {e}")
            failed += 1

    ftp.quit()
    print(f"Deployment complete: {success} uploaded, {failed} failed.")

if __name__ == '__main__':
    upload_files()
