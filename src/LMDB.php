<?php
declare(strict_types=1);
namespace iggyvolz\lmdb;
use FFI;
class LMDB
{
    public const FIXEDMAP = 0x01;
    public const NOSUBDIR = 0x4000;
    public const NOSYNC = 0x10000;
    public const RDONLY = 0x20000;
    public const NOMETASYNC = 0x40000;
    public const WRITEMAP = 0x80000;
    public const MAPASYNC = 0x100000;
    public const NOTLS = 0x200000;
    public const NOLOCK = 0x400000;
    public const NORDAHEAD = 0x800000;
    public const NOMEMINIT = 0x1000000;
    public const REVERSEKEY = 0x02;
    public const DUPSORT = 0x04;
    public const INTEGERKEY = 0x08;
    public const DUPFIXED = 0x10;
    public const INTEGERDUP = 0x20;
    public const REVERSEDUP = 0x40;
    public const CREATE = 0x40000;
    public const NOOVERWRITE = 0x10;
    public const NODUPDATA = 0x20;
    public const CURRENT = 0x40;
    public const RESERVE = 0x10000;
    public const APPEND = 0x20000;
    public const APPENDDUP = 0x40000;
    public const MULTIPLE = 0x80000;
    public const CP_COMPACT = 0x01;

    public const SUCCESS = 0;
    public const KEYEXIST = -30799;
    public const NOTFOUND = -30798;
    public const PAGE_NOTFOUND = -30797;
    public const CORRUPTED = -30796;
    public const PANIC = -30795;
    public const VERSION_MISMATCH = -30794;
    public const INVALID = -30793;
    public const MAP_FULL = -30792;
    public const DBS_FULL = -30791;
    public const READERS_FULL = -30790;
    public const TLS_FULL = -30789;
    public const TXN_FULL = -30788;
    public const CURSOR_FULL = -30787;
    public const PAGE_FULL = -30786;
    public const MAP_RESIZED = -30785;
    public const INCOMPATIBLE = -30784;
    public const BAD_RSLOT = -30783;
    public const BAD_TXN = -30782;
    public const BAD_VALSIZE = -30781;
    public const BAD_DBI = -30780;
    
    public const FIRST = 0;
    public const FIRST_DUP = 1;
    public const GET_BOTH = 2;
    public const GET_BOTH_RANGE = 3;
    public const GET_CURRENT = 4;
    public const GET_MULTIPLE = 5;
    public const LAST = 6;
    public const LAST_DUP = 7;
    public const NEXT = 8;
    public const NEXT_DUP = 9;
    public const NEXT_MULTIPLE = 10;
    public const NEXT_NODUP = 11;
    public const PREV = 12;
    public const PREV_DUP = 13;
    public const PREV_NODUP = 14;
    public const SET = 15;
    public const SET_KEY = 16;
    public const SET_RANGE = 17;
    public const PREV_MULTIPLE = 18;
    private FFI $ffi;
    private function __construct()
    {
        $this->ffi = FFI::cdef(<<<'EOT'
        typedef unsigned int mode_t;
        typedef long unsigned int size_t;
        typedef mode_t mdb_mode_t;
        typedef int mdb_filehandle_t;
        typedef struct MDB_env MDB_env;
        typedef struct MDB_txn MDB_txn;
        typedef unsigned int MDB_dbi;
        typedef struct MDB_cursor MDB_cursor;
        typedef struct MDB_val {
         size_t mv_size;
         void *mv_data;
        } MDB_val;
        typedef int (MDB_cmp_func)(const MDB_val *a, const MDB_val *b);
        typedef void (MDB_rel_func)(MDB_val *item, void *oldptr, void *newptr, void *relctx);
        typedef enum MDB_cursor_op {
         MDB_FIRST,
         MDB_FIRST_DUP,
         MDB_GET_BOTH,
         MDB_GET_BOTH_RANGE,
         MDB_GET_CURRENT,
         MDB_GET_MULTIPLE,
         MDB_LAST,
         MDB_LAST_DUP,
         MDB_NEXT,
         MDB_NEXT_DUP,
         MDB_NEXT_MULTIPLE,
         MDB_NEXT_NODUP,
         MDB_PREV,
         MDB_PREV_DUP,
         MDB_PREV_NODUP,
         MDB_SET,
         MDB_SET_KEY,
         MDB_SET_RANGE,
         MDB_PREV_MULTIPLE
        } MDB_cursor_op;
        typedef struct MDB_stat {
         unsigned int ms_psize;
         unsigned int ms_depth;
         size_t ms_branch_pages;
         size_t ms_leaf_pages;
         size_t ms_overflow_pages;
         size_t ms_entries;
        } MDB_stat;
        typedef struct MDB_envinfo {
         void *me_mapaddr;
         size_t me_mapsize;
         size_t me_last_pgno;
         size_t me_last_txnid;
         unsigned int me_maxreaders;
         unsigned int me_numreaders;
        } MDB_envinfo;
        char *mdb_version(int *major, int *minor, int *patch);
        char *mdb_strerror(int err);
        int mdb_env_create(MDB_env **env);
        int mdb_env_open(MDB_env *env, const char *path, unsigned int flags, mdb_mode_t mode);
        int mdb_env_copy(MDB_env *env, const char *path);
        int mdb_env_copyfd(MDB_env *env, mdb_filehandle_t fd);
        int mdb_env_copy2(MDB_env *env, const char *path, unsigned int flags);
        int mdb_env_copyfd2(MDB_env *env, mdb_filehandle_t fd, unsigned int flags);
        int mdb_env_stat(MDB_env *env, MDB_stat *stat);
        int mdb_env_info(MDB_env *env, MDB_envinfo *stat);
        int mdb_env_sync(MDB_env *env, int force);
        void mdb_env_close(MDB_env *env);
        int mdb_env_set_flags(MDB_env *env, unsigned int flags, int onoff);
        int mdb_env_get_flags(MDB_env *env, unsigned int *flags);
        int mdb_env_get_path(MDB_env *env, const char **path);
        int mdb_env_get_fd(MDB_env *env, mdb_filehandle_t *fd);
        int mdb_env_set_mapsize(MDB_env *env, size_t size);
        int mdb_env_set_maxreaders(MDB_env *env, unsigned int readers);
        int mdb_env_get_maxreaders(MDB_env *env, unsigned int *readers);
        int mdb_env_set_maxdbs(MDB_env *env, MDB_dbi dbs);
        int mdb_env_get_maxkeysize(MDB_env *env);
        int mdb_env_set_userctx(MDB_env *env, void *ctx);
        void *mdb_env_get_userctx(MDB_env *env);
        typedef void MDB_assert_func(MDB_env *env, const char *msg);
        int mdb_env_set_assert(MDB_env *env, MDB_assert_func *func);
        int mdb_txn_begin(MDB_env *env, MDB_txn *parent, unsigned int flags, MDB_txn **txn);
        MDB_env *mdb_txn_env(MDB_txn *txn);
        size_t mdb_txn_id(MDB_txn *txn);
        int mdb_txn_commit(MDB_txn *txn);
        void mdb_txn_abort(MDB_txn *txn);
        void mdb_txn_reset(MDB_txn *txn);
        int mdb_txn_renew(MDB_txn *txn);
        int mdb_dbi_open(MDB_txn *txn, const char *name, unsigned int flags, MDB_dbi *dbi);
        int mdb_stat(MDB_txn *txn, MDB_dbi dbi, MDB_stat *stat);
        int mdb_dbi_flags(MDB_txn *txn, MDB_dbi dbi, unsigned int *flags);
        void mdb_dbi_close(MDB_env *env, MDB_dbi dbi);
        int mdb_drop(MDB_txn *txn, MDB_dbi dbi, int del);
        int mdb_set_compare(MDB_txn *txn, MDB_dbi dbi, MDB_cmp_func *cmp);
        int mdb_set_dupsort(MDB_txn *txn, MDB_dbi dbi, MDB_cmp_func *cmp);
        int mdb_set_relfunc(MDB_txn *txn, MDB_dbi dbi, MDB_rel_func *rel);
        int mdb_set_relctx(MDB_txn *txn, MDB_dbi dbi, void *ctx);
        int mdb_get(MDB_txn *txn, MDB_dbi dbi, MDB_val *key, MDB_val *data);
        int mdb_put(MDB_txn *txn, MDB_dbi dbi, MDB_val *key, MDB_val *data, unsigned int flags);
        int mdb_del(MDB_txn *txn, MDB_dbi dbi, MDB_val *key, MDB_val *data);
        int mdb_cursor_open(MDB_txn *txn, MDB_dbi dbi, MDB_cursor **cursor);
        void mdb_cursor_close(MDB_cursor *cursor);
        int mdb_cursor_renew(MDB_txn *txn, MDB_cursor *cursor);
        MDB_txn *mdb_cursor_txn(MDB_cursor *cursor);
        MDB_dbi mdb_cursor_dbi(MDB_cursor *cursor);
        int mdb_cursor_get(MDB_cursor *cursor, MDB_val *key, MDB_val *data, MDB_cursor_op op);
        int mdb_cursor_put(MDB_cursor *cursor, MDB_val *key, MDB_val *data, unsigned int flags);
        int mdb_cursor_del(MDB_cursor *cursor, unsigned int flags);
        int mdb_cursor_count(MDB_cursor *cursor, size_t *countp);
        int mdb_cmp(MDB_txn *txn, MDB_dbi dbi, const MDB_val *a, const MDB_val *b);
        int mdb_dcmp(MDB_txn *txn, MDB_dbi dbi, const MDB_val *a, const MDB_val *b);
        typedef int (MDB_msg_func)(const char *msg, void *ctx);
        int mdb_reader_list(MDB_env *env, MDB_msg_func *func, void *ctx);
        int mdb_reader_check(MDB_env *env, int *dead);
        EOT, "liblmdb.so");
    }
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return self::get()->ffi->$name(...$arguments);
    }
    private static ?LMDB $instance = null;
    private static function get(): self
    {
        return self::$instance ??= new self();
    }
    public static function getVersion(): string
    {
        $major = self::new("int");
        $minor = self::new("int");
        $patch = self::new("int");
        self::mdb_version(FFI::addr($major),FFI::addr($minor),FFI::addr($patch));
        return "$major.$minor.$patch";
    }
    public static function assert(int $result): void
    {
        if($result !== 0) {
            throw new LMDBException($result);
        }
    }
}